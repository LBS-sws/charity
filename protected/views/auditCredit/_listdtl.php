<?php
$fun_id = $this->record['type']==2?"GA01":"GA03";
?>
<tr class='clickable-row <?php echo $this->record['style']; ?>' data-href='<?php echo $this->getLink($fun_id, 'auditCredit/edit', 'auditCredit/view', array('index'=>$this->record['id'],'type'=>$this->record['type']));?>'>


    <td><?php echo $this->drawEditButton($fun_id, 'auditCredit/edit', 'edit', array('index'=>$this->record['id'],'type'=>$this->record['type'])); ?></td>



    <td><?php echo $this->record['employee_name']; ?></td>
    <td><?php echo $this->record['city']; ?></td>
    <td><?php echo $this->record['charity_name']; ?></td>
    <td><?php echo $this->record['credit_point']; ?></td>
    <td><?php echo $this->record['apply_date']; ?></td>
    <td><?php echo $this->record['status']; ?></td>
</tr>
