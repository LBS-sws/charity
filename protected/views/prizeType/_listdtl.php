<tr class='clickable-row' data-href='<?php echo $this->getLink('SS02', 'prizeType/edit', 'prizeType/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('SS02', 'prizeType/edit', 'prizeType/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['prize_name']; ?></td>
	<td><?php echo $this->record['prize_point']; ?></td>
	<td><?php echo $this->record['z_index']; ?></td>
</tr>
