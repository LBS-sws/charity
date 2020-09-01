<tr class='clickable-row <?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('GA02', 'auditPrize/edit', 'auditPrize/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('GA02', 'auditPrize/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['prize_name']; ?></td>
    <td><?php echo $this->record['apply_num']; ?></td>
    <td><?php echo $this->record['total_point']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
