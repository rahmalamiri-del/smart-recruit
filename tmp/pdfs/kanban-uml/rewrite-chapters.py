from pathlib import Path
root=Path('H:/smart-recruit/output/latex/smartrecruit-memoire-kanban-uml')
chap3=r'''\chapter{Conception UML et architecture de SmartRecruit}
\label{chap:conception}

\section{Objectifs et périmètre de la conception}
La conception traduit les exigences du chapitre précédent en responsabilités, structures et scénarios vérifiables. Elle utilise UML pour représenter les points de vue complémentaires du système : les services attendus par les acteurs, les objets manipulés, l'ordre des échanges et les changements d'état. UML est un langage de modélisation ; il ne remplace ni le processus Kanban ni les critères d'acceptation des cartes \cite{omg-uml}. Dans ce projet, une carte prête à être réalisée doit identifier le modèle concerné et les règles à respecter. Une modification de ces règles implique de relire le modèle avant la vérification.

Les figures décrivent le code examiné le 13 septembre 2026, avec un niveau d'abstraction adapté au mémoire. Les classes métier sont des représentations des informations persistées : le projet n'implémente pas une classe Eloquent par entité. Les routes sont majoritairement définies par des fonctions anonymes dans \path{routes/web.php} ; \code{SqlStore} fournit les opérations de persistance et retourne des tableaux aux vues. Cette distinction permet de lire le diagramme de classes sans lui attribuer une organisation objet absente du code.

\section{Diagramme de cas d'utilisation}
Les trois acteurs métier sont l'étudiant, le recruteur et l'administrateur. L'interface actuelle emploie également le mot \emph{candidat} pour l'espace associé au rôle technique \code{student}. Dans le périmètre académique de SmartRecruit, ce candidat est l'étudiant qui présente son parcours et recherche un stage. L'inscription et la connexion constituent des préalables communs ; les opérations protégées supposent une session valable et le rôle approprié.

L'étudiant gère ses informations et consulte les offres publiées. Le recruteur publie ses offres, examine les profils et suit les candidatures de son périmètre. L'administrateur intervient sur l'ensemble des comptes et des objets métier, y compris les fonctions de restauration. Les associations de la figure~\ref{fig:uml-usecases} expriment la participation aux cas d'utilisation, et non un ordre d'exécution. Les contraintes de propriété complètent le rôle : être recruteur ne suffit pas pour modifier l'offre d'un autre compte.

\begin{figure}[H]\centering
\resizebox{.9\textwidth}{!}{\input{figures/uml-cas-utilisation.tex}}
\caption{Diagramme UML des cas d'utilisation des trois acteurs métier}
\label{fig:uml-usecases}\end{figure}

Ce modèle soutient notamment les cartes K01 et K02 relatives à l'identité et aux droits, ainsi que K09 et K10 pour les espaces de suivi et l'administration. Chaque association doit être traduite en scénarios positifs et négatifs : accès autorisé pour le bon acteur, refus ou absence d'action pour un tiers. La visibilité d'un bouton ne constitue pas, à elle seule, une autorisation.

\section{Diagramme de classes métier et persistance}
Le noyau relationnel comprend sept ensembles : comptes, profils étudiants, profils recruteurs, documents CV, offres, candidatures et scores. Le compte porte l'identité et le rôle ; les profils portent les attributs propres à l'acteur. La candidature relie une offre à un étudiant. Le score conserve les composantes d'une comparaison associée à une candidature. Le document CV décrit une ressource stockée dans le système de fichiers, dont le chemin et le résultat de traitement sont référencés dans la base.

La figure~\ref{fig:uml-classes} adopte des multiplicités correspondant aux contraintes relationnelles observées. Une référence de profil vers un compte est obligatoire, mais la migration ne rend pas \code{user_id} unique dans les tables de profils. La création applicative produit le profil adapté au rôle ; un véritable un-à-un imposé par SQL nécessiterait une contrainte supplémentaire. De même, la table des scores n'impose pas une seule ligne par candidature, même si le magasin actualise un résultat existant dans son fonctionnement courant.

\begin{figure}[H]\centering
\resizebox{\textwidth}{!}{\input{figures/uml-classes.tex}}
\caption{Diagramme de classes des informations métier et de leurs associations}
\label{fig:uml-classes}\end{figure}

L'unicité du couple offre--profil étudiant empêche la présence de deux candidatures persistées pour la même association. Cette contrainte ne dispense pas de traiter correctement deux requêtes concurrentes. La suppression d'un recruteur peut laisser une offre sans propriétaire selon les mécanismes relationnels ou applicatifs concernés ; l'administration dispose d'une réaffectation. Les suppressions logiques doivent être distinguées des cascades SQL, qui concernent un effacement physique. Les champs JSON de compétences ou de métadonnées sont des colonnes structurées de MySQL, pas un mécanisme de stockage alternatif.

Les classes de la figure n'exposent pas d'opérations artificiellement attribuées aux entités. Les opérations effectives, comme \code{addOffer}, \code{apply}, \code{updateApplicationStatus} et \code{saveMatchScore}, sont regroupées dans \code{SqlStore}. La table de correspondance détaillée entre noms de tables et attributs est fournie dans l'annexe~\ref{ann:dictionnaire}.

\section{Architecture et diagramme de composants}
La séparation retenue distingue présentation, orchestration métier, persistance et traitement de texte. Blade génère les pages et les formulaires. Les routes reçoivent les requêtes, contrôlent les paramètres et sollicitent les services. Le magasin relationnel encapsule les lectures et les écritures. Le service Python expose un contrat HTTP ; le client PHP transforme les paramètres locaux en charges utiles et restitue une réponse exploitable par l'orchestration.

\begin{figure}[H]\centering
\resizebox{\textwidth}{!}{\input{figures/uml-composants.tex}}
\caption{Diagramme de composants et dépendances principales}
\label{fig:uml-composants}\end{figure}

MySQL est la source métier sélectionnée par \code{sr_store}. Si la base est indisponible, les routes concernées renvoient une erreur 503 ; elles ne basculent plus vers \code{DemoStore}. Cette dernière classe reste présente pour des usages de démonstration et la représentation de l'invité. Le mécanisme de repli réellement actif dans le parcours de recommandation concerne le calcul : \code{SemanticMatcher} fournit une réponse locale lorsque le service distant ne fournit pas un score exploitable.

Cette architecture rend possible une évolution du moteur sans changer les formulaires. Elle ne garantit pas automatiquement l'équivalence des deux calculs ni la validité des documents. Les contrats de données et les erreurs constituent donc des éléments de conception à part entière. Les cartes K04, K07 et K11 portent respectivement le traitement du CV, le score et la cohérence de persistance.

\section{Séquence d'enregistrement d'une candidature}
Le scénario commence lorsque l'étudiant déclenche le formulaire de candidature. Le traitement vérifie la session, le rôle, l'offre et l'existence du profil associé. Le magasin recherche ensuite l'association offre--profil avant de créer une nouvelle ligne. Le chemin nominal conserve un état initial \code{submitted}. Si une association existe déjà, le magasin applique sa logique de restitution ou de réactivation au lieu de créer une seconde candidature.

\begin{figure}[H]\centering
\resizebox{\textwidth}{!}{\input{figures/uml-sequence-candidature.tex}}
\caption{Séquence UML simplifiée du dépôt d'une candidature}
\label{fig:uml-seq-candidature}\end{figure}

La figure~\ref{fig:uml-seq-candidature} suppose des contrôles préalables réussis. Les branches d'erreur restent des scénarios de vérification distincts : compte incorrect, profil manquant, offre absente, indisponibilité SQL ou conflit d'unicité. La présence d'une vérification avant insertion n'établit pas l'idempotence sous concurrence ; un essai avec deux demandes simultanées devra vérifier le message retourné et l'état final de la base.

L'enregistrement de la candidature et le calcul du classement ne doivent pas être confondus. La recommandation peut comparer le vivier de profils avant qu'ils ne postulent. Une personne classée n'est donc pas nécessairement candidate à l'offre. Les interfaces distinguent cette situation par l'absence d'action de décision lorsqu'aucune candidature n'est liée au profil.

\section{Séquence de recommandation et repli de calcul}
L'orchestration construit les textes et les ensembles de compétences puis appelle le moteur PHP. Ce résultat est calculé avant l'éventuel appel distant. Si le diagnostic préalable annonce le service disponible, \code{AiClient} interroge \code{POST /match}. Une réponse contenant un score remplace les champs correspondants du résultat local ; une absence de réponse exploitable laisse le résultat PHP disponible. La séquence suivante représente une comparaison ; le classement répète cette opération sur les éléments à comparer puis les trie.

\begin{figure}[H]\centering
\resizebox{\textwidth}{!}{\input{figures/uml-sequence-recommandation.tex}}
\caption{Séquence UML d'une comparaison avec branche distante optionnelle}
\label{fig:uml-seq-match}\end{figure}

Le fragment \emph{opt} représente la sollicitation conditionnelle du service, et le fragment \emph{alt} distingue les résultats retenus. Dans la version étudiée, la présence du champ score constitue le contrôle central ; un schéma complet de validation des types et des bornes reste à renforcer. Une réponse distante peut ainsi être partiellement combinée avec les valeurs locales. Le chapitre~\ref{chap:moteur} précise les différences entre les formules et les référentiels, afin que cette continuité de service ne soit pas interprétée comme une identité de résultats.

\section{Activité de traitement d'un CV}
L'importation associe saisie structurée, fichier facultatif et texte manuel. Pour un PDF, le parseur sollicite d'abord le service Python lorsqu'il est disponible dans le client ; un texte vide ou un échec entraîne une extraction locale. Le texte extrait est ensuite concaténé avec le texte manuel. Les compétences et les coordonnées sont détectées sur ce résultat. L'avertissement repose sur une longueur extraite inférieure à quarante caractères, lorsqu'un fichier a été fourni et qu'aucun texte manuel ne compense cette faiblesse.

\begin{figure}[H]\centering
\resizebox{.88\textwidth}{!}{\input{figures/uml-activite-cv.tex}}
\caption{Diagramme d'activité du traitement du CV et de sa restitution}
\label{fig:uml-activite-cv}\end{figure}

La figure décrit le parcours de création ou de mise à jour après validation des champs. Le stockage d'un fichier et l'écriture des données ne forment pas automatiquement une transaction atomique. Un échec SQL après déplacement du document peut nécessiter une compensation. Ce point justifie un critère de vérification spécifique sur les fichiers orphelins. Le marqueur de fiabilité est également une heuristique : un texte long peut rester incohérent et un document court peut être pertinent. Il informe l'utilisateur sans certifier l'extraction.

\section{États et suivi des candidatures}
La candidature possède quatre valeurs de suivi : reçue, présélectionnée, acceptée et refusée. Le code conserve les identifiants \code{submitted}, \code{shortlisted}, \code{interview} et \code{rejected}. Le libellé affiché pour \code{interview} est « Acceptée » ; il ne représente pas une embauche définitive. La route de mise à jour accepte ces valeurs sans imposer un enchaînement linéaire. Un même statut peut donc être choisi à nouveau et une décision antérieure peut être modifiée par un acteur habilité.

\begin{figure}[H]\centering
\resizebox{.9\textwidth}{!}{\input{figures/uml-etats-candidature.tex}}
\caption{États métier d'une candidature et visibilité après suppression logique}
\label{fig:uml-etats}\end{figure}

La figure~\ref{fig:uml-etats} présente la distribution vers les quatre valeurs autorisées, et sépare cette dimension de la suppression logique. Une candidature supprimée conserve son statut pour sa restauration. Ce modèle évite de confondre « refusée » et « supprimée » : le premier est une décision visible dans le suivi, le second retire l'élément des listes actives. Les boutons actuellement exposés aux recruteurs mettent surtout en avant l'acceptation et le refus, bien que le contrat de route connaisse quatre valeurs.

\section{Synthèse et traçabilité vers la réalisation}
La conception associe chaque besoin à une représentation adaptée. Les cas d'utilisation structurent les accès, les classes expliquent les données, les composants délimitent les dépendances, les séquences décrivent les échanges et les diagrammes d'activité et d'états exposent les décisions. Avant de déplacer une carte Kanban vers Vérification, les écarts entre modèle et implémentation doivent être identifiés. Le chapitre suivant présente les choix de réalisation et les interfaces qui rendent ces fonctions accessibles aux utilisateurs.
'''
root.joinpath('chapters/03-conception.tex').write_text(chap3,encoding='utf-8')

chap4=r'''\chapter{Réalisation de la plateforme et interfaces web}
\label{chap:realisation}

\section{Organisation de la réalisation avec Kanban}
La réalisation est présentée selon les capacités définies par les cartes K01 à K12. Le flux commun est Réserve, Prêt, En cours, Vérification et Terminé. Une limite d'une carte en réalisation et d'une carte en vérification, soit deux engagements simultanés au maximum, constitue le réglage proposé pour un travail individuel. Cette organisation rétrospective rend le périmètre lisible ; elle ne constitue pas un historique attesté de mouvements de cartes pendant le stage.

Chaque capacité relie une action utilisateur à un contrôle. Par exemple, K03 ne se limite pas à afficher un formulaire de profil : il faut retrouver les informations saisies dans une restitution cohérente. K07 ne se limite pas à produire une note : les compétences, la provenance du calcul et les différences entre moteurs doivent être compréhensibles. K10 associe la suppression à sa restauration, afin de définir l'effet attendu sur les listes et les relations.

Le passage vers Vérification intervient lorsque l'implémentation et les éléments de preuve sont prêts à être examinés. Pour les interfaces, ces éléments comprennent le rendu de la page, les états vides ou d'erreur, la conservation des champs et la navigation selon le rôle. Le passage vers Terminé reste dépendant du périmètre accepté : une vérification visuelle n'autorise pas à déclarer une transaction métier validée sur MySQL.

\section{Environnement et choix techniques}
Le projet utilise PHP et Laravel pour l'application web, Blade pour les vues et MySQL pour les données métier. Les échanges avec le composant d'analyse reposent sur un service Python exposé par Flask. L'interface utilise une feuille CSS commune, sans dépendance à un framework JavaScript de rendu côté client. Les modèles HTML sont générés sur le serveur, ce qui permet de conserver des formulaires classiques et des contrôles d'accès dans les routes \cite{laravel-routing,laravel-validation}.

\begin{table}[H]\centering\small
\caption{Éléments de l'environnement observé et responsabilités}
\begin{tabularx}{\textwidth}{@{}P{3.6cm}Y@{}}\toprule
Élément & Usage dans SmartRecruit\\\midrule
PHP / Laravel & Requêtes web, session, validation, autorisations et orchestration.\\
Blade / CSS & Gabarit commun, tableaux de bord, formulaires et adaptation aux écrans.\\
MySQL / SqlStore & Persistance relationnelle des sept ensembles métier.\\
Python / Flask & Extraction de documents et seconde implémentation du score.\\
Git, configuration et scripts & Artefacts de développement décrits lorsqu'ils sont présents ; aucun historique de commits n'est supposé.\\
\bottomrule\end{tabularx}\end{table}

Le choix d'un service spécialisé isole les bibliothèques de traitement de documents. L'application conserve néanmoins un moteur PHP afin de fournir un résultat local lorsque le service distant ne répond pas. Cette séparation exige de maintenir un contrat de données stable et des erreurs compréhensibles. Le service de santé confirme la réponse du composant Python ; il ne certifie pas la disponibilité de la base ni la validité d'un CV particulier.

\section{Structure de l'application}
Le fichier \path{routes/web.php} contient l'essentiel de l'orchestration HTTP. Les classes du répertoire \path{app/Support} regroupent les fonctions de persistance, d'extraction, de calcul et de communication. Les vues sont réparties entre authentification, tableaux de bord, offres, étudiants et administration. Le gabarit \path{resources/views/layout.blade.php} compose la navigation et le contenu commun ; les composants \code{partials.logo}, \code{partials.navigation} et \code{partials.main} évitent de répéter leur structure sur chaque écran.

Le modèle conceptuel du chapitre précédent est traduit par les migrations et les requêtes du magasin. Le code utilise le constructeur de requêtes Laravel plutôt qu'une collection de modèles Eloquent. Cette organisation concentre la conversion des lignes SQL en structures utilisées par les vues. Elle rend aussi important le contrôle des méthodes volumineuses du magasin, car plusieurs règles métier et conventions de présentation y sont regroupées.

\section{Identité visuelle et navigation}
L'identité de SmartRecruit associe un symbole de deux maillons à une palette vert profond et menthe. Le signe évoque la mise en relation des profils et des entreprises ; son format vectoriel conserve sa netteté dans la navigation et dans l'icône du navigateur. La charte de l'application utilise des fonds clairs, des titres hiérarchisés et des actions principales contrastées.

Les espaces connectés disposent d'une navigation latérale sur ordinateur. Les destinations sont construites à partir du rôle : tableau de bord, offres, profil ou vivier, puis administration pour le compte habilité. Sur petit écran, la même liste est présentée dans un menu dépliable. Les informations importantes restent accessibles sans imposer un chargement d'application monopage. Le lien « Aller au contenu », les noms accessibles et les contours de focus facilitent la navigation au clavier.

Les tableaux conservent leurs en-têtes et peuvent défiler horizontalement sur mobile. Les formulaires utilisent des sections cohérentes : identité, informations professionnelles, compétences, document et accès selon le cas. Les boutons destructifs sont distingués des actions principales. Les messages de succès ou d'erreur occupent une zone commune, avec des rôles de restitution adaptés. Cette cohérence visuelle ne modifie pas les destinations des formulaires ni les noms des champs attendus par les routes.

\section{Implémentation des parcours métier}
\subsection{Inscription, session et autorisations}
La connexion compare le mot de passe saisi au hash enregistré. L'inscription publique limite le choix à étudiant ou recruteur et crée le compte ainsi que le profil correspondant. Les informations utiles de session excluent le hash. Le rôle et l'existence du compte sont revalidés depuis la base lorsque celle-ci est accessible ; la suppression d'un compte ou une modification de rôle ne doit donc pas rester sans effet jusqu'à la prochaine connexion.

La protection combine middleware de rôle et contrôles de propriété. Un recruteur intervient sur les offres qui lui appartiennent, tandis qu'un étudiant modifie son profil. L'administration dispose d'un périmètre plus large. La consultation des CV utilise une route dédiée, avec les contrôles prévus par l'application. Il reste nécessaire de vérifier le serveur de fichiers pour s'assurer qu'un chemin directement exposé ne contourne pas ces contrôles.

\subsection{Profil, CV et correction des informations}
Le profil rassemble titre, formation, expérience, compétences et liens professionnels. Un fichier peut compléter ces informations. Le parseur assemble le texte obtenu et le texte manuel, extrait les informations puis retourne un résultat accompagné d'un indicateur de fiabilité. L'utilisateur conserve la possibilité de corriger les compétences et coordonnées. Un résultat automatique est ainsi traité comme une aide à la saisie, et non comme une preuve définitive sur le parcours.

La modification du profil accepte le remplacement d'un CV. Le retrait du document est présenté séparément de la suppression du profil. Cette distinction correspond à des effets différents : retirer un fichier ne signifie pas retirer l'étudiant de l'application. Le déplacement du fichier et la sauvegarde SQL doivent également être examinés ensemble lors des essais d'erreur, afin de ne pas laisser de document sans référence.

\subsection{Offres, classement et candidatures}
Une offre contient un titre, une entreprise, une localisation, un type, des compétences et une description. Les états brouillon, publiée et fermée structurent sa visibilité. Le recruteur retrouve ses offres et leur suivi ; l'administrateur peut filtrer l'ensemble et réaffecter une offre. Pour l'étudiant, les offres publiées sont rapprochées du profil puis présentées selon leur pertinence calculée.

Le classement d'une offre peut inclure des profils du vivier qui n'ont pas encore candidaté. Les actions de décision sont rattachées aux candidatures effectivement présentes. Le score principal, les compétences communes et celles à vérifier fournissent des éléments d'explication. Le moteur ne prend pas la décision de recrutement : l'acteur habilité examine le profil, le CV et les éléments professionnels utiles avant de modifier le statut.

\subsection{Administration et restauration}
La console d'administration réunit la gestion des comptes, des offres, des profils et des candidatures. Les opérations de suppression logique retirent les éléments des listes actives sans constituer un effacement définitif. Les vues de corbeille permettent leur restauration. Les formulaires de confirmation expliquent les objets concernés et les effets associés. Les critères des cartes K02, K06 et K10 doivent couvrir les conséquences de ces opérations sur les droits, les relations et les doublons éventuels.

\input{chapters/04-interfaces.tex}

\section{Intégration, limites et préparation du déploiement}
Le magasin actif est MySQL. La fonction \code{sr_store} échoue explicitement lorsque la base n'est pas disponible. L'ancien magasin JSON reste un artefact du dépôt et un support d'éléments de démonstration, sans être sélectionné comme persistance de substitution par les routes métier actuelles. Une chaîne de diagnostic ancienne ne suffit pas à établir l'existence d'un repli actif.

La séparation entre démonstration et usage réel demande toutefois un contrôle complémentaire : l'appel \code{all()} conserve un chemin d'initialisation ou de synchronisation de données de démonstration. Ce comportement explique pourquoi les captures du mémoire ont été produites sans ouvrir les routes métier contre la base existante. Avant un déploiement, l'initialisation doit devenir une opération explicite et maîtrisée.

Les paramètres de connexion, les secrets, les chemins de stockage et les ports relèvent de l'environnement. La présence de fichiers Docker ou d'un serveur local n'est pas une preuve de qualification d'une installation. La documentation Laravel et celle de Docker fournissent les mécanismes à configurer ; leur application doit ensuite être vérifiée sur un environnement isolé \cite{laravel-deployment,docker-compose}. Les fichiers CV nécessitent notamment une politique de conservation, des droits adaptés et un accès contrôlé.

La réalisation fournit ainsi une chaîne utilisateur cohérente, de la constitution du profil au suivi d'une candidature. Les captures rendent visibles les choix de présentation ; les contrats et le code expliquent leur fonctionnement. Le chapitre de validation précise la portée des preuves disponibles et les travaux à inscrire dans la réserve Kanban pour consolider cette base.
'''
root.joinpath('chapters/04-realisation.tex').write_text(chap4,encoding='utf-8')
print('Chapitres 3 et 4 réécrits.')
