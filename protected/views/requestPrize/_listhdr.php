<tr>
    <th>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('employee_name').$this->drawOrderArrow('d.name'),'#',$this->createOrderLink('requestPrize-list','d.name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('city_name').$this->drawOrderArrow('d.city'),'#',$this->createOrderLink('requestPrize-list','d.city'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('prize_name').$this->drawOrderArrow('b.prize_name'),'#',$this->createOrderLink('requestPrize-list','b.prize_name'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('apply_num').$this->drawOrderArrow('a.apply_num'),'#',$this->createOrderLink('requestPrize-list','a.apply_num'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('prize_point').$this->drawOrderArrow('total_point'),'#',$this->createOrderLink('requestPrize-list','total_point'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('apply_date').$this->drawOrderArrow('a.apply_date'),'#',$this->createOrderLink('requestPrize-list','a.apply_date'))
        ;
        ?>
    </th>
    <th>
        <?php echo TbHtml::link($this->getLabelName('state').$this->drawOrderArrow('a.state'),'#',$this->createOrderLink('requestPrize-list','a.state'))
        ;
        ?>
    </th>
</tr>
