<?php 
foreach ($alertas as $keys => $mensajes): 
    foreach($mensajes as $mensaje): ?>
        
        <div class="alerta <?php echo $keys?>"  >
            <?php echo $mensaje; ?>
        </div>

<?php 
    endforeach; 
endforeach; ?>