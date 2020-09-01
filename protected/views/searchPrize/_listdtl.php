<tr class='clickable-row' data-href='<?php echo $this->getLink('SR03', 'searchPrize/view', 'searchPrize/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->needHrefButton('SR03', 'searchPrize/view', 'view', array('index'=>$this->record['id'])); ?></td>


    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['prize_name']; ?></td>
    <td><?php echo $this->record['apply_num']; ?></td>
    <td><?php echo $this->record['total_point']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
</tr>
