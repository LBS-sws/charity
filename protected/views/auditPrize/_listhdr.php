<tr>
    <th>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('auditPrize-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('auditPrize-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('prize_name').$this->drawOrderArrow('b.prize_name'),'#',$this->createOrderLink('auditPrize-list','b.prize_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('apply_num').$this->drawOrderArrow('a.apply_num'),'#',$this->createOrderLink('auditPrize-list','a.apply_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('prize_point').$this->drawOrderArrow('total_point'),'#',$this->createOrderLink('auditPrize-list','total_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('auditPrize-list','a.apply_date'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('state').$this->drawOrderArrow('a.state'),'#',$this->createOrderLink('auditPrize-list','a.state'))
        ;
        ?>
    </th>
</tr>
