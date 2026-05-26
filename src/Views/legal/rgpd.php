<main class="legal-page">
    <div class="container">
        <h1>Politique de confidentialité (RGPD)</h1>
        <p class="legal-date">Dernière mise à jour : <?= date('d/m/Y') ?></p>

        <h2>1. Responsable du traitement</h2>
        <p>
            Le responsable du traitement des données collectées via l'application Jobflow est :<br>
            <strong>Alex Berrel</strong><br>
            Email : <a href="mailto:alex.berrel@gmail.com">alex.berrel@gmail.com</a>
        </p>

        <h2>2. Données collectées</h2>
        <p>Dans le cadre de l'utilisation de Jobflow, les données suivantes sont collectées et traitées :</p>
        <ul>
            <li><strong>Données d'identification :</strong> nom, prénom</li>
            <li><strong>Données de contact :</strong> adresse email, numéro de téléphone</li>
            <li><strong>Données entreprises :</strong> raison sociale, numéro SIRET, adresse postale</li>
            <li><strong>Données financières :</strong> chiffre d'affaires, revenus déclarés dans l'outil</li>
        </ul>
        <p>Ces données sont saisies directement par l'utilisateur et stockées sur nos serveurs dans une base de données sécurisée.</p>
        <p>
            <strong>Données relatives à des tiers :</strong> dans le cadre de la gestion CRM, l'utilisateur est susceptible de saisir des données personnelles concernant ses contacts (prospects, clients). En tant que responsable de traitement pour ces données tierces, l'utilisateur s'engage à respecter les obligations RGPD applicables.
        </p>

        <h2>3. Finalités du traitement</h2>
        <p>Les données collectées sont traitées aux fins suivantes :</p>
        <ul>
            <li>Création et gestion du compte utilisateur</li>
            <li>Gestion du CRM personnel (contacts, prospects, clients)</li>
            <li>Suivi de l'activité commerciale freelance</li>
            <li>Génération de tableaux de bord et statistiques de revenus</li>
            <li>Autocomplétion d'adresse via l'API adresse.data.gouv.fr</li>
            <li>Vérification d'entreprises via l'API annuaire-entreprises.data.gouv.fr</li>
        </ul>

        <h2>4. Base légale</h2>
        <p>Les traitements sont fondés sur les bases légales suivantes :</p>
        <ul>
            <li><strong>Exécution du contrat</strong> (art. 6.1.b RGPD) : pour la gestion du compte et l'accès au service</li>
            <li><strong>Consentement</strong> (art. 6.1.a RGPD) : pour tout traitement optionnel, recueilli lors de la création de compte ou de l'utilisation de fonctionnalités spécifiques</li>
            <li><strong>Intérêt légitime</strong> (art. 6.1.f RGPD) : pour l'amélioration du service et la sécurisation de l'application</li>
        </ul>

        <h2>5. Destinataires des données</h2>
        <p>Les données ne sont <strong>pas vendues ni transmises à des tiers à des fins commerciales</strong>. Elles peuvent être partagées avec :</p>
        <ul>
            <li>
                <strong>API Adresse — adresse.data.gouv.fr</strong> (La Poste / Etalab) : autocomplétion d'adresses —
                seule la saisie partielle de l'adresse est transmise, sans donnée identifiante
            </li>
            <li>
                <strong>API Annuaire des entreprises — annuaire-entreprises.data.gouv.fr</strong> (DINUM) :
                recherche SIRET/SIREN — seul le numéro SIRET est transmis
            </li>
            <li>
                <strong>InfinityFree / iFastNet</strong> (hébergeur) : les données sont hébergées sur leurs serveurs
                dans le cadre de la prestation d'hébergement. iFastNet LTD — Bulman House Regent Centre, Coxlodge,
                Newcastle Upon Tyne, Royaume-Uni — <a href="https://ifastnet.com" target="_blank" rel="noopener noreferrer">ifastnet.com</a>
            </li>
        </ul>
        <p>
            <strong>Transfert hors UE :</strong> les serveurs d'InfinityFree peuvent être localisés en dehors de l'Union Européenne (notamment aux États-Unis). Dans ce cas, le transfert est encadré par les clauses contractuelles types (CCT) de la Commission Européenne ou toute autre garantie appropriée.
        </p>

        <h2>6. Durée de conservation</h2>
        <ul>
            <li><strong>Données de compte :</strong> conservées pendant toute la durée d'utilisation active du compte, puis supprimées dans un délai de <strong>30 jours</strong> suivant la suppression du compte</li>
            <li><strong>Données CRM (contacts, revenus) :</strong> conservées pendant la durée d'utilisation active, supprimées avec le compte</li>
            <li><strong>Données de logs :</strong> conservées au maximum <strong>12 mois</strong></li>
        </ul>

        <h2>7. Droits des utilisateurs</h2>
        <p>Conformément au RGPD (articles 15 à 22), vous disposez des droits suivants :</p>
        <ul>
            <li><strong>Droit d'accès</strong> (art. 15) : obtenir une copie de vos données</li>
            <li><strong>Droit de rectification</strong> (art. 16) : corriger des données inexactes</li>
            <li><strong>Droit à l'effacement</strong> (art. 17) : demander la suppression de votre compte et de vos données</li>
            <li><strong>Droit à la portabilité</strong> (art. 20) : recevoir vos données dans un format structuré et lisible par machine</li>
            <li><strong>Droit d'opposition</strong> (art. 21) : vous opposer à un traitement fondé sur l'intérêt légitime</li>
            <li><strong>Droit à la limitation</strong> (art. 18) : restreindre temporairement un traitement en cas de litige</li>
            <li><strong>Droit de retrait du consentement</strong> (art. 7) : à tout moment, sans effet rétroactif</li>
        </ul>
        <p>
            Pour exercer ces droits : <a href="mailto:alex.berrel@gmail.com">alex.berrel@gmail.com</a> — réponse sous <strong>30 jours</strong>.<br>
            En cas de réponse insatisfaisante, vous pouvez introduire une réclamation auprès de la
            <strong>CNIL</strong> : <a href="https://www.cnil.fr" target="_blank" rel="noopener noreferrer">www.cnil.fr</a>
        </p>

        <h2>8. Sécurité</h2>
        <p>
            Des mesures techniques et organisationnelles sont mises en place pour protéger vos données :
            connexions sécurisées (HTTPS), mots de passe hachés (bcrypt), accès restreint à la base de données,
            sessions PHP sécurisées. Aucun système n'étant infaillible, nous ne pouvons garantir une sécurité absolue.
        </p>

        <h2>9. Cookies et stockage local</h2>
        <p>
            Jobflow utilise des <strong>cookies de session</strong> strictement nécessaires au fonctionnement de l'application (authentification). Ces cookies ne nécessitent pas de consentement au titre de l'article 82 de la loi Informatique et Libertés.<br>
            Aucun cookie publicitaire ou de tracking tiers n'est utilisé.
        </p>

        <h2>10. Mineurs</h2>
        <p>
            Jobflow est destiné à des utilisateurs majeurs exerçant une activité professionnelle. L'inscription de mineurs est interdite.
        </p>
    </div>
</main>