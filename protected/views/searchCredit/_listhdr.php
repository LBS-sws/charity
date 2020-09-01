<tr>
    <th>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_code').$this->drawOrderArrow('d.code'),'#',$this->createOrderLink('searchCredit-list','d.code'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('searchCredit-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('searchCredit-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('credit_name').$this->drawOrderArrow('b.charity_name'),'#',$this->createOrderLink('searchCredit-list','b.charity_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('credit_point').$this->drawOrderArrow('a.credit_point'),'#',$this->createOrderLink('searchCredit-list','a.credit_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('searchCredit-list','a.apply_date'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('exp_date').$this->drawOrderArrow('a.lcd'),'#',$this->createOrderLink('searchCredit-list','a.lcd'))
        ;
        ?>
    </th>
</tr>
