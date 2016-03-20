{{ form("Scan/update", "method": "post", "name":"frmObject","id":"frmObject") }}
<fieldset>
    <legend>Modifier le nom d'un disque</legend>
    <div class="alert alert-info">Disque {{ disque.getNom() }}</div>
    <div class="form-group">
        <input type="text" name="nom" id="nom" value="{{ disque.getNom() }}"/>
        <input type="hidden" name="id" id="id" value="{{ disque.getId() }}"/>
        <input type="hidden" name="idUtil" id="idUtil" value="{{ disque.getIdUtilisateur() }}"/>
        <input type="submit" value="Confirmer" class="btn btn-default"/>
    </div>
</fieldset>
</form>