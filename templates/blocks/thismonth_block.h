<{if $block.contents}>
    <{foreach item=event from=$block.contents}>
        <li><{$event}></li>
    <{/foreach}>    
<{/if}>