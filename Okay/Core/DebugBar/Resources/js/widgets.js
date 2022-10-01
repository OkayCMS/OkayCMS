(function($) {
    var csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');

    /**
     * Widget for the TimeDataCollector
     *
     * Options:
     *  - data
     */

    var highlight = PhpDebugBar.Widgets.highlight = function(code, lang) {
        if (typeof(code) === 'string') {
            if (typeof(hljs) === 'undefined') {
                return htmlize(code);
            }
            if (lang) {
                return hljs.highlight(lang, code).value;
            }
            return hljs.highlightAuto(code).value;
        }

        if (typeof(hljs) === 'object') {
            code.each(function(i, e) { hljs.highlightBlock(e); });
        }
        return code;
    };

    var createCodeBlock = PhpDebugBar.Widgets.createCodeBlock = function(code, lang, firstLineNumber, highlightedLine) {
        var pre = $('<pre />').addClass(csscls('code-block'));
        // Add a newline to prevent <code> element from vertically collapsing too far if the last
        // code line was empty: that creates problems with the horizontal scrollbar being
        // incorrectly positioned - most noticeable when line numbers are shown.
        var codeElement = $('<code />').text(code + '\n').appendTo(pre);

        // Add a span with a special class if we are supposed to highlight a line.  highlight.js will
        // still correctly format code even with existing markup in it.
        if ($.isNumeric(highlightedLine)) {
            if ($.isNumeric(firstLineNumber)) {
                highlightedLine = highlightedLine - firstLineNumber + 1;
            }
            codeElement.html(function (index, html) {
                var currentLine = 1;
                return html.replace(/^.*$/gm, function(line) {
                    if (currentLine++ == highlightedLine) {
                        return '<span class="' + csscls('highlighted-line') + '">' + line + '</span>';
                    } else {
                        return line;
                    }
                });
            });
        }

        // Format the code
        if (lang) {
            pre.addClass("language-" + lang);
        }
        highlight(pre);

        // Show line numbers in a list
        if ($.isNumeric(firstLineNumber)) {
            var lineCount = code.split('\n').length;
            var $lineNumbers = $('<ul />').prependTo(pre);
            pre.children().addClass(csscls('numbered-code'));
            for (var i = firstLineNumber; i < firstLineNumber + lineCount; i++) {
                $('<li />').text(i).appendTo($lineNumbers);
            }
        }

        return pre;
    };

    var OkayTimelineWidget = PhpDebugBar.Widgets.OkayTimelineWidget = PhpDebugBar.Widget.extend({

        tagName: 'ul',

        className: csscls('timeline'),

        render: function() {
            this.bindAttr('data', function(data) {

                // ported from php DataFormatter
                var formatDuration = function(seconds) {
                    if (seconds < 0.001)
                        return (seconds * 1000000).toFixed() + 'μs';
                    else if (seconds < 1)
                        return (seconds * 1000).toFixed(2) + 'ms';
                    return (seconds).toFixed(2) +  's';
                };

                this.$el.empty();
                if (data.measures) {
                    var aggregate = {},
                        aggregatedLines = {};

                    for (var i = 0; i < data.measures.length; i++) {
                        var measure = data.measures[i];

                        // Блок агрегации времени
                        if(!aggregate[measure.name])
                            aggregate[measure.name] = { count: 0, duration: 0, label: measure.label };

                        aggregate[measure.name]['count'] += 1;
                        aggregate[measure.name]['duration'] += measure.duration;

                        var m,
                            li,
                            left = (measure.relative_start * 100 / data.duration).toFixed(2),
                            width = Math.min((measure.duration * 100 / data.duration).toFixed(2), 100 - left);

                        // Если запись нужно агрегировать на таймлайне, то найдём предыдущую запись. Иначе создадим новую
                        if (measure.aggregate && aggregatedLines[measure.name]) {
                            li = aggregatedLines[measure.name].element;
                            aggregatedLines[measure.name].duration += measure.duration;

                            m = li.find(`div.${csscls('measure')}`)
                            m.find('.'+csscls('label')).text(measure.label + " (" + formatDuration(aggregatedLines[measure.name].duration) + ")");
                        } else {
                            aggregatedLines[measure.name] = {
                                element: li = $('<li />'),
                                duration: measure.duration
                            };

                            li.css('cursor', 'pointer').click(function() {
                                $(this).find('table').toggle();
                            });

                            this.$el.append(li);
                            m = $('<div />').addClass(csscls('measure'))
                            m.append($('<span />').addClass(csscls('label')).text(measure.label + " (" + measure.duration_str + ")"));
                            m.appendTo(li);
                        }

                        // Рисуем бар
                        m.append($('<span />').addClass(csscls('value')).css({
                            left: left + "%",
                            width: width + "%"
                        })).prop('title', measure.duration_str);

                        if (measure.collector) {
                            $('<span />').addClass(csscls('collector')).text(measure.collector).appendTo(m);
                        }

                        if (measure.params && !$.isEmptyObject(measure.params)) {
                            var table = $('<table><tr><th>Action</th><th>Info</th><th>Time</th></tr></table>').addClass(csscls('params')).appendTo(li);
                            for (var key in measure.params) {
                                if (typeof measure.params[key] !== 'function') {
                                    table.append('<tr><td class="' + csscls('name') + '">' + key + '</td><td class="' + csscls('value') +
                                    '"><pre><code>' + measure.params[key] + '</code></pre></td><td>' + measure.duration_str + '</td></tr>');
                                }
                            }
                        }
                    }

                    // convert to array and sort by duration
                    aggregate = $.map(aggregate, function(data) {
                       return {
                           label: data.label,
                           data: data
                       }
                    }).sort(function(a, b) {
                        return b.data.duration - a.data.duration
                    });

                    // build table and add
                    var aggregateTable = $('<table style="display: table; border: 0; width: 99%"></table>').addClass(csscls('params'));
                    $.each(aggregate, function(i, aggregate) {
                        width = Math.min((aggregate.data.duration * 100 / data.duration).toFixed(2), 100);

                        aggregateTable.append('<tr><td class="' + csscls('name') + '">' + aggregate.data.count + ' x ' + aggregate.label + ' (' + width + '%)</td><td class="' + csscls('value') + '">' +
                            '<div class="' + csscls('measure') +'">' +
                                '<span class="' + csscls('value') + '" style="width:' + width + '%"></span>' +
                                '<span class="' + csscls('label') + '">' + formatDuration(aggregate.data.duration) + '</span>' +
                            '</div></td></tr>');
                    });

                    this.$el.append('<li/>').find('li:last').append(aggregateTable);
                }
            });
        }
    });

    // ------------------------------------------------------------------

    /**
     * An extension of KVListWidget where the data represents a list
     * of variables
     *
     * Options:
     *  - data
     */
    var OkayVariableListWidget = PhpDebugBar.Widgets.OkayVariableListWidget = PhpDebugBar.Widgets.KVListWidget.extend({

        className: csscls('kvlist varlist'),

        render: function() {
            this.bindAttr(['itemRenderer', 'data'], function() {
                this.$el.empty();
                if (!this.has('data')) {
                    return;
                }

                var self = this;
                $.each(this.get('data'), function(key, values) {
                    var dt = $('<dt />').addClass(csscls('key')).appendTo(self.$el);

                    let dds = [];

                    for (let params of values) {
                        dds.push($('<dd />').addClass(csscls('value')).appendTo(self.$el));
                    }
                    self.get('itemRenderer')(dt, dds, key, values);
                });
            });
        },

        itemRenderer: function(dt, dds, key, values) {
            $('<span />').attr('title', key).text(key).appendTo(dt);

            for (let [key, params] of values.entries()) {
                let dd = dds[key];

                let v = params.value;
                if (v && v.length > 100) {
                    v = v.substr(0, 100) + "...";
                }
                let prettyVal = null;

                dd.text(v).click(function() {
                    if (dd.hasClass(csscls('pretty'))) {
                        dd.text(v).removeClass(csscls('pretty'));
                    } else {
                        prettyVal = prettyVal || createCodeBlock(value);
                        dd.addClass(csscls('pretty')).empty().append(prettyVal);
                    }
                });
            }
        }
    });
})(PhpDebugBar.$);
