<tr class='clickable-row <?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink('CY02', 'requestPrize/edit', 'requestPrize/view', array('index'=>$this->record['id']));?>'>


    <td><?php echo $this->drawEditButton('CY02', 'requestPrize/edit', 'edit', array('index'=>$this->record['id'])); ?></td>



    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['prize_name']; ?></td>
    <td><?php echo $this->record['apply_num']; ?></td>
    <td><?php echo $this->record['total_point']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
