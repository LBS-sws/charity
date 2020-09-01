<tr>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_code').$this->drawOrderArrow('d.code'),'#',$this->createOrderLink('searchSum-list','d.code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('searchSum-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('searchSum-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('year').$this->drawOrderArrow('a.year'),'#',$this->createOrderLink('searchSum-list','a.year'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('start_num').$this->drawOrderArrow('start_num'),'#',$this->createOrderLink('searchSum-list','start_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('end_num').$this->drawOrderArrow('end_num'),'#',$this->createOrderLink('searchSum-list','end_num'))
        ;
        ?>
    </th>
</tr>
