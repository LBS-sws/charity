<tr>
    <th></th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('charity_code').$this->drawOrderArrow('charity_code'),'#',$this->createOrderLink('creditType-list','charity_code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('charity_name').$this->drawOrderArrow('charity_name'),'#',$this->createOrderLink('creditType-list','charity_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('charity_point').$this->drawOrderArrow('charity_point'),'#',$this->createOrderLink('creditType-list','charity_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('validity').$this->drawOrderArrow('validity'),'#',$this->createOrderLink('creditType-list','validity'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('bumen_ex').$this->drawOrderArrow('bumen_ex'),'#',$this->createOrderLink('creditType-list','bumen_ex'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('rule').$this->drawOrderArrow('rule'),'#',$this->createOrderLink('creditType-list','rule'))
        ;
        ?>
    </th>
</tr>
