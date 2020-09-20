<ul class="pagination-list">
    <li class="pagination-item pagination-item-prev"><a <?=$backHref?>>Назад</a></li>
    <?php
    for($i = 1; $i <= $pageCount; $i++)
    {
        if( $curPage === $i):?>
            <li class="pagination-item pagination-item-active"><a><?=$i?></a></li>
        <?php else: ?>
            <li class="pagination-item"><a href="<?=$uri.$i?>"><?=$i?></a></li>
        <?php endif;
    }
    ?>
    <li class="pagination-item pagination-item-next"><a <?=$forwardHref?>>Вперед</a></li>
</ul>
