{% if userCo %}
<h1>Mes disques -> {{ user.login }} ({{ user.prenom }} {{ user.nom }})</h1>
    {{ liste }}
{% else %}
    Vous devez vous connecter pour accéder a cette page
{% endif %}

{{ script_foot }}