{{ form("Disque/update", "method": "post", "name":"frmNom","id":"frmNom") }}
<fieldset>
    <legend>Modifier le nom d'un disque</legend>
    <div class="alert alert-info">Disque {{ disque.getNom() }}</div>
    <div class="form-group">
        <input type="text" name="nom" id="nom" value="{{ disque->getNom() }}"/>
        <input type="submit" value="Confirmer" class="btn btn-default">
        </div>
</fieldset>
{{ script_foot }}