(function () {
    const csscls = PhpDebugBar.utils.makecsscls('phpdebugbar-widgets-');
    const createCodeBlock = PhpDebugBar.Widgets.createCodeBlock;

    class OkayTimelineWidget extends PhpDebugBar.Widgets.TimelineWidget {
    }

    class OkayVariableListWidget extends PhpDebugBar.Widgets.KVListWidget {
        get className() {
            return csscls('kvlist varlist');
        }

        render() {
            this.bindAttr(['itemRenderer', 'data'], function () {
                this.el.innerHTML = '';
                if (!this.has('data')) {
                    return;
                }

                for (const [key, values] of Object.entries(this.get('data'))) {
                    const dt = document.createElement('dt');
                    dt.classList.add(csscls('key'));
                    this.el.append(dt);

                    const dds = [];
                    for (const ignored of values) {
                        const dd = document.createElement('dd');
                        dd.classList.add(csscls('value'));
                        this.el.append(dd);
                        dds.push(dd);
                    }

                    this.get('itemRenderer')(dt, dds, key, values);
                }
            });
        }

        itemRenderer(dt, dds, key, values) {
            const title = document.createElement('span');
            title.setAttribute('title', key);
            title.textContent = key;
            dt.append(title);

            values.forEach((params, index) => {
                const dd = dds[index];
                let value = String(params.value ?? '');
                const shortValue = value.length > 100 ? `${value.substring(0, 100)}...` : value;
                let prettyValue = null;

                dd.textContent = shortValue;
                dd.addEventListener('click', () => {
                    if (window.getSelection().type === 'Range') {
                        return;
                    }

                    if (dd.classList.contains(csscls('pretty'))) {
                        dd.textContent = shortValue;
                        dd.classList.remove(csscls('pretty'));
                        return;
                    }

                    prettyValue = prettyValue || createCodeBlock(value);
                    dd.classList.add(csscls('pretty'));
                    dd.innerHTML = '';
                    dd.append(prettyValue);
                });
            });
        }
    }

    PhpDebugBar.Widgets.OkayTimelineWidget = OkayTimelineWidget;
    PhpDebugBar.Widgets.OkayVariableListWidget = OkayVariableListWidget;
})();
