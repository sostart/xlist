<?php foreach ($lists as $row): ?>
    <div class="project" 
        data-id="<?php echo $row['id']; ?>" 
        data-pid="<?php echo $row['pid']; ?>" 
        data-sort="<?php echo $row['sort']; ?>" 
        data-expand="<?php echo $row['is_expand']; ?>" 
        data-completed="<?php echo $row['is_completed']; ?>" 
    >
        <div class="row"">
          <div class="expand"><span style="cursor:pointer;" class="glyphicon glyphicon-plus"></span></div>
          <div class="dot<?php if (isset($row['sub']) && $row['sub']) echo ' dot-have-children'; ?>">&nbsp;</div>
          <div class="title" contenteditable><?php echo $row['title']; ?></div>
          <div class="status"><span class="glyphicon glyphicon-pause"></span><span class="glyphicon glyphicon-stop"></span></div>
          <div class="members" contenteditable><?php echo $row['members']; ?></div>
        </div>
        <div class="children"><?php if (isset($row['sub'])&&$row['sub']) { echo View::render('row', ['lists'=>$row['sub']]); }?></div>
    </div>
<?php endforeach; ?>
