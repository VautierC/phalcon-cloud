<?php echo $this->tag->form(array('Scan/update', 'method' => 'post', 'name' => 'frmObject', 'id' => 'frmObject')); ?>
<fieldset>
    <legend>Modifier le nom d'un disque</legend>
    <div class="alert alert-info">Disque <?php echo $disque->getNom(); ?></div>
    <div class="form-group">
        <input type="text" name="nom" id="nom" value="<?php echo $disque->getNom(); ?>"/>
        <input type="hidden" name="id" id="id" value="<?php echo $disque->getId(); ?>"/>
        <input type="hidden" name="idUtil" id="idUtil" value="<?php echo $disque->getIdUtilisateur(); ?>"/>
        <input type="submit" value="Confirmer" class="btn btn-default"/>
    </div>
</fieldset>
</form>
<?php echo $script_foot; ?>