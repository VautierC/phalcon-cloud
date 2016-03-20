{{ form("Disques/update", "method": "post", "name":"frmObject","id":"frmObject") }}
<fieldset>
    <legend>Création d'un disque</legend>
    <div class="alert alert-info">Disque {{ disque.getNom() }}</div>
    <div class="form-group">
        <table>
            <tr>
                <td><label for="id">ID du disque</label></td>
                <td><input type="number" name="id" id="id" value="" min="0"/></td>
            </tr>
            <tr>
                <td><label for="nom">Nom du Disque :</label></td>
                <td><input type="text" name="nom" id="nom" value=""/></td>
            </tr>
            <tr>
                <td><label for="idUtil">ID Utilisateur</label></td>
                <td><input type="number" name="idUtil" id="idUtil" value="" min="0"/></td>
            </tr>
        </table>
        <input type="submit" value="Confirmer" class="btn btn-default"/>
    </div>
</fieldset>
</form>