<tr>
    <th>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('auditCredit-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('auditCredit-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('charity_name').$this->drawOrderArrow('b.charity_name'),'#',$this->createOrderLink('auditCredit-list','b.charity_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('credit_point').$this->drawOrderArrow('a.credit_point'),'#',$this->createOrderLink('auditCredit-list','a.credit_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('auditCredit-list','a.apply_date'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('state').$this->drawOrderArrow('a.state'),'#',$this->createOrderLink('auditCredit-list','a.state'))
        ;
        ?>
    </th>
</tr>
