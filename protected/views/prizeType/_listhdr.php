<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('prize_name').$this->drawOrderArrow('prize_name'),'#',$this->createOrderLink('prizeType-list','prize_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('prize_point').$this->drawOrderArrow('prize_point'),'#',$this->createOrderLink('prizeType-list','prize_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('z_index').$this->drawOrderArrow('z_index'),'#',$this->createOrderLink('prizeType-list','z_index'))
        ;
        ?>
    </th>
</tr>
