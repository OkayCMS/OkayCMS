{if $user_comments}
    <div class="comment">
        {function name=comments_tree level=0}
            {foreach $comments as $comment}
                <div class="comment__item {if $level > 0} admin_note{/if}">
                    {* Comment anchor *}
                    <a name="comment_{$comment->id}"></a>
                    {* Comment list *}
                    <div class="comment__inner">
                        <div class="comment__icon">
                            {if $level > 0}
                                {include file="svg.tpl" svgId="comment-admin_icon"}
                            {else}
                                {include file="svg.tpl" svgId="comment-user_icon"}
                            {/if}
                        </div>
                        <div class="comment__boxed">
                            <div class="d-flex flex-wrap align-items-center justify-content-between comment__header">
                                {* Comment name *}
                                <div class="d-flex flex-wrap align-items-center comment__author">
                                    <span class="comment__name">{$comment->name|escape}</span>
                                    {* Comment status *}
                                    {if !$comment->approved}
                                        <span class="comment__status" data-language="post_comment_status">({$lang->post_comment_status})</span>
                                    {/if}
                                </div>
                                {* Comment date *}
                                <div class="comment__date">
                                    <span>{$comment->date|date}, {$comment->date|time}</span>
                                </div>
                            </div>

                            {* Comment content *}
                            <div class="comment__body">
                                {$comment->text|escape|nl2br}
                            </div>

                            {if $comment->type == "product"}
                                <a href="{url_generator route="product" url=$comment->product->url}#comment_{$comment->id}">{$comment->product->name|escape}</a>
                            {elseif $comment->type == "post"}
                                <a href="{url_generator route="post" url=$comment->post->url}#comment_{$comment->id}">{$comment->post->name|escape}</a>
                            {/if}

                        </div>
                    </div>
                    {if !empty($comment->children)}
                        {comments_tree comments=$comment->children level=$level+1}
                    {/if}
                </div>
            {/foreach}
        {/function}
        {comments_tree comments=$user_comments}
    </div>
{else}
    <div class="boxed_no_comments">
        <div class="boxed_no_comments__icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" viewBox="0 0 20 19.805">
                <path fill="#D1D6D8" d="M4,19.8V16H2a2,2,0,0,1-2-2V2A2,2,0,0,1,2,0H18a2,2,0,0,1,2,2V14a2,2,0,0,1-2,2H10.087L4,19.8H4ZM2,14H6v2.2L9.513,14H18V2H2Zm3-3V9h7v2ZM5,7V5h9V7Z"></path>
            </svg>
        </div>
        <div class="boxed_no_comments__title">
            <span data-language="product_no_comments">{$lang->product_no_comments}</span>
        </div>
    </div>
{/if}