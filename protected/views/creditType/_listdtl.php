<tr class='clickable-row' data-href='<?php echo $this->getLink('SS01', 'creditType/edit', 'creditType/view', array('index'=>$this->record['id']));?>'>
	<td><?php echo $this->drawEditButton('SS01', 'creditType/edit', 'creditType/view', array('index'=>$this->record['id'])); ?></td>
	<td><?php echo $this->record['charity_code']; ?></td>
	<td><?php echo $this->record['charity_name']; ?></td>
	<td><?php echo $this->record['charity_point']; ?></td>
	<td><?php echo $this->record['validity']; ?></td>
	<td><?php echo $this->record['bumen_ex']; ?></td>
	<td><?php echo $this->record['rule']; ?></td>
</tr>
