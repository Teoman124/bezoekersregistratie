# Opdrachten – Bezoekersregistratie

Uitbreidingsopdrachten voor de bestaande Laravel-applicatie *Bezoekersregistratie*.
Bedoeld voor **MBO niveau 4 – Software Developer**. Elke opdracht is een losse user story die
een student (of duo) binnen de bestaande applicatie kan oppakken en opleveren.

> **Voor de docent:** Opdracht 1 (Evacuatielijst) is volledig uitgewerkt inclusief lesplanning,
> testaanwijzingen en beoordelingsrubric. De opdrachten 2 t/m 7 zijn als complete user stories
> uitgewerkt (scenario, acceptatiecriteria, hints, inschatting) en kunnen op dezelfde manier
> worden ingezet. Laat de student een **feature-branch** maken, met **tests** werken en de keuzes
> kunnen **verantwoorden** (code review).

---

## Hoe gebruik je deze opdrachten?

- **Scenario eerst.** Elke opdracht begint met een realistisch verhaal. Lees dat hardop voor bij de kick-off.
- **Gelaagd.** Iedere opdracht heeft een *basis* (voldoende), *uitbreiding* (goed) en *expert-uitdaging* (excellent).
- **Bouw voort op bestaande code.** De hints verwijzen naar bestaande bestanden en patronen in déze applicatie.
- **Testen is verplicht** (projectregel): schrijf bij elke opdracht minimaal één Pest-test en draai die.
- **Werkwijze:** `git checkout -b feature/<naam>` → bouwen → `vendor/bin/pint --dirty` → `php artisan test` → demo → reflectie.

### Overzicht & moeilijkheidsgraad

| #  | Opdracht                                   | Focus                        | Niveau        | Inschatting |
|----|--------------------------------------------|------------------------------|---------------|-------------|
| 1  | Evacuatielijst (BHV)                       | Query hergebruik + view      | ⭐ Instap      | 1 dagdeel   |
| 2  | Bezoekerspas met QR-code                   | Genereren + printen          | ⭐⭐ Gemiddeld | 1–2 dagen   |
| 3  | Digitale uitnodiging & pre-registratie     | Tokens + publieke route      | ⭐⭐⭐ Pittig   | 2–3 dagen   |
| 4  | Geheimhoudingsverklaring (NDA) bij check-in| Formulier + opslag + audit   | ⭐⭐ Gemiddeld | 1–2 dagen   |
| 5  | Dashboard-grafiek bezoeken per dag         | Data-visualisatie (front-end)| ⭐⭐ Gemiddeld | 1 dag       |
| 6  | No-show signalering                        | Scheduling + statuslogica    | ⭐⭐⭐ Pittig   | 2 dagen     |
| 7  | Bezoeker-blacklist (toegangsweigering)     | Validatie + autorisatie      | ⭐⭐ Gemiddeld | 1–2 dagen   |

---

# Opdracht 1 — Evacuatielijst voor BHV ⭐

## Scenario

Je werkt als developer aan het bezoekersregistratiesysteem van een bedrijf. Tijdens een
BHV-oefening (Bedrijfshulpverlening) gaat het mis: zodra het brandalarm afgaat, weet de
receptie **niet** welke bezoekers er op dat moment in het pand zijn. Ze moeten het uit losse
schermen bij elkaar zoeken — veel te traag bij een echte ontruiming.

De BHV-coördinator vraagt:

> *"Ik wil met één klik een lijst van alle bezoekers die nú binnen zijn, om mee te nemen naar de
> verzamelplaats. Daarop moet staan wie het is, van welk bedrijf, bij wie ze op bezoek zijn en
> hoe laat ze binnenkwamen."*

## User story

**Als** BHV'er / receptiemedewerker
**wil ik** met één klik een overzicht zien van alle bezoekers die op dit moment ingecheckt zijn
(en nog niet uitgecheckt)
**zodat** ik bij een ontruiming snel kan controleren of iedereen veilig buiten is.

## Acceptatiecriteria — Basis (voldoende)

1. Er is een nieuwe pagina bereikbaar via een knop/menu-item **"Evacuatielijst"** (bijv. route `/Visits/evacuation`).
2. De lijst toont **alleen** bezoeken met status `active` (wél ingecheckt, nog níet uitgecheckt).
3. Per regel is zichtbaar: **naam bezoeker**, **bedrijf**, **naam host-medewerker**, **incheck-tijd**.
4. Bovenaan staat een teller: *"Aantal aanwezige bezoekers: X"*.
5. De pagina toont het **moment van genereren** (datum + tijd) zodat een uitdraai te dateren is.
6. De pagina is alleen toegankelijk voor de rollen **`admin`** en **`employee`** (niet voor `visitor`).
7. Als er niemand binnen is, verschijnt een nette melding: *"Er zijn momenteel geen bezoekers in het pand."*

## Uitbreiding (goed)

- **Printvriendelijke weergave**: knop "Print" + print-CSS (geen sidebar/menu op papier).
- **Filter op afdeling**: toon alleen aanwezige bezoekers van een gekozen afdeling.
- **Verblijfsduur**: toon per bezoeker hoe lang ze al binnen zijn (bijv. "1 u 24 min").
- **Auto-refresh**: de lijst ververst automatisch elke 30 seconden.

## Expert-uitdaging (excellent)

- **PDF-export** van de evacuatielijst (sluit aan op de bestaande export-knop bij Bezoeken).
- **Groeperen per host-medewerker**, zodat zichtbaar is wie verantwoordelijk is voor welke bezoeker.
- **Afvink-functie**: per bezoeker aanvinken "gezien bij verzamelplaats", inclusief opslag van die status.

## Technische hints (scaffolding)

- De query bestaat al deels. In `app/Models/Visit.php` staat een scope:
  ```php
  Visit::active()->with(['visitor.user', 'employee.user'])->get();
  ```
  Gebruik `with()` om N+1-queries te voorkomen (de naam van de bezoeker zit op `visitor->user->name`).
- Voeg de route toe in `routes/web.php` **binnen** de bestaande groep
  `middleware(['auth', 'check.role:admin,employee'])`.
  ⚠️ **Let op de volgorde:** zet `/Visits/evacuation` *vóór* de route `/Visits/{visit}`,
  anders ziet Laravel "evacuation" aan voor een visit-id.
- Voeg een methode `evacuation()` toe aan `VisitController`. Bekijk hoe `active()` en `history()`
  daar al werken — kopieer dat patroon.
- Maak de view `resources/views/Visits/evacuation.blade.php`. Gebruik `active.blade.php` als
  voorbeeld voor de tabelopmaak.
- De status kun je tonen met de bestaande methode `currentStatus()` op het `Visit`-model.
- Verblijfsduur kan met Carbon: `$visit->check_in_time->diffForHumans(null, true)`.

## Testen (verplicht)

Maak een test: `php artisan make:test --pest EvacuationListTest`

Dek minimaal af:
1. Een **ingecheckte** bezoeker (check_in gezet, check_out leeg) **staat** in de lijst.
2. Een **uitgecheckte** bezoeker **staat er niet** in.
3. Een gebruiker met rol **`visitor`** krijgt een **403** (geen toegang).

Gebruik de bestaande factories. Voorbeeldskelet:

```php
it('toont alleen ingecheckte bezoekers op de evacuatielijst', function () {
    $employee = User::factory()->create(['role' => 'employee']);

    $aanwezig = Visit::factory()->create([
        'check_in_time' => now()->subHour(),
        'check_out_time' => null,
    ]);
    $vertrokken = Visit::factory()->create([
        'check_in_time' => now()->subHours(3),
        'check_out_time' => now()->subHour(),
    ]);

    $this->actingAs($employee)
        ->get('/Visits/evacuation')
        ->assertOk()
        ->assertSee($aanwezig->visitor->user->name)
        ->assertDontSee($vertrokken->visitor->user->name);
});
```

> Controleer of er een `VisitFactory` met geschikte states bestaat; zo niet, vul de relaties
> (`visitor_id`, `host_employee_id`) zelf met factories.

## Lesplanning (90 minuten)

| Fase                   | Tijd      | Inhoud                                                                 |
|------------------------|-----------|-----------------------------------------------------------------------|
| Activerende start      | 10 min    | Brandalarm-scenario bespreken. *"Wie is er nú in dit lokaal?"* Laat zien dat de data al bestaat (`check_in_time`). |
| Instructie / demo      | 15 min    | Live coding: route toevoegen op de juiste plek, controllermethode met `Visit::active()`. Routevolgorde-valkuil voordoen. |
| Begeleid oefenen       | 20 min    | Samen de view bouwen op basis van `active.blade.php`. Teller + leeg-melding. |
| Zelfstandig werken     | 35 min    | Studenten maken basis af + starten met een uitbreiding + schrijven de test. |
| Afsluiting / reflectie | 10 min    | 1–2 demo's. Bespreken: waarom is dit een veiligheids-/wettelijke eis? Wat is het verschil tussen `active` en `planned`? |

## Beoordelingsrubric

| Criterium          | Onvoldoende                         | Voldoende                              | Goed                                   | Excellent                                         |
|--------------------|-------------------------------------|----------------------------------------|----------------------------------------|---------------------------------------------------|
| **Functionaliteit**| Lijst werkt niet / verkeerde data   | Alle basis-criteria werken             | Basis + ≥1 uitbreiding                 | Basis + meerdere uitbreidingen + expert-uitdaging |
| **Codekwaliteit**  | Query in de view, code gedupliceerd | Hergebruikt `scopeActive`, nette controller | Eager loading, geen N+1, heldere namen | Volgt projectconventies, Pint-clean               |
| **Autorisatie**    | Iedereen kan erbij                   | Juiste rollen via `check.role`         | Getest met meerdere rollen             | Edge cases afgedekt                               |
| **Testen**         | Geen test                           | 1 werkende test                        | 3 tests (aanwezig/afwezig/403)         | Tests dekken ook uitbreidingen                    |
| **Verantwoording** | Kan keuzes niet uitleggen           | Legt hoofdkeuzes uit                   | Onderbouwt query- en routekeuzes       | Reflecteert op alternatieven en BHV-context       |

---

# Opdracht 2 — Bezoekerspas met QR-code ⭐⭐

## Scenario

De receptie wil dat elke bezoeker bij binnenkomst een **bezoekerspas** krijgt om op te spelden.
Op die pas staan de naam, het bedrijf, de gastheer en de datum — plus een **QR-code** die later
gescand kan worden om snel uit te checken. Nu schrijft de receptie alles met de hand op een sticker.

## User story

**Als** receptiemedewerker
**wil ik** na het inchecken een printbare bezoekerspas met QR-code kunnen genereren
**zodat** de bezoeker herkenbaar is in het pand en snel kan uitchecken.

## Acceptatiecriteria — Basis

1. Op de detailpagina van een bezoek (`visits.show`) staat een knop **"Pas afdrukken"** (alleen voor ingecheckte bezoeken).
2. De pas toont: **naam bezoeker**, **bedrijf**, **gastheer**, **datum**, en de tekst **"BEZOEKER"**.
3. Op de pas staat een **QR-code** die verwijst naar de bezoek-detailpagina (of het bezoek-id bevat).
4. De pas heeft een **printvriendelijke** weergave (eigen layout, geen sidebar/menu).

## Uitbreiding (goed)

- QR-code linkt naar de **uitcheck-actie**, zodat scannen meteen uitcheckt.
- Bedrijfslogo en huisstijlkleuren op de pas.
- Pas-formaat netjes op een vaste maat (bijv. 85×54 mm, creditcardformaat) met print-CSS.

## Expert-uitdaging (excellent)

- Pas als **PDF** downloadbaar maken.
- **Badge-nummer** genereren en opslaan bij het bezoek (uniek per dag).

## Technische hints

- Voor de QR-code is een package nodig (bijv. `simple-qrcode` of `bacon/bacon-qr-code`).
  ⚠️ **Dependencies wijzigen alleen na overleg met de docent** (projectregel). Bespreek de keuze eerst.
- Een QR-code kan ook puur client-side met een JS-library (bijv. `qrcode.js`) — dan geen Composer-dependency nodig.
- Gebruik een **named route** voor de QR-inhoud: `route('visits.show', $visit)` of `route('visits.checkout', $visit)`.
- Maak een aparte Blade-view `Visits/badge.blade.php` zonder de standaard layout (of een minimale print-layout).
- Print-CSS: `@media print { ... }` en eventueel `@page { size: 85mm 54mm; }`.

## Testen

- Test dat de pas-route een **200** geeft voor een ingecheckt bezoek en de naam van de bezoeker toont.
- Test dat de pas **niet** beschikbaar is (knop/403) voor een nog niet ingecheckt bezoek (als je die regel toevoegt).

---

# Opdracht 3 — Digitale uitnodiging & pre-registratie ⭐⭐⭐

## Scenario

Nu maakt een medewerker élk bezoek handmatig aan met alle gegevens van de bezoeker. Dat kost tijd
en de gegevens kloppen vaak niet. Het bedrijf wil dat de medewerker een **uitnodiging** stuurt met
een link, waarna de **bezoeker zelf** zijn gegevens invult vóór hij langskomt. Bij aankomst hoeft
de receptie alleen nog op "inchecken" te klikken.

## User story

**Als** medewerker
**wil ik** een bezoeker een uitnodiging met unieke link kunnen sturen waarmee die zich zelf
pre-registreert
**zodat** de bezoekersgegevens vooraf compleet en correct in het systeem staan.

## Acceptatiecriteria — Basis

1. Een medewerker kan een uitnodiging aanmaken met **e-mailadres**, **verwachte datum/tijd** en **reden van bezoek**.
2. Het systeem genereert een **uniek, niet te raden token** (bijv. UUID) en een bijbehorende publieke link.
3. Via die link komt de bezoeker op een **publiek formulier** (geen login nodig) om zijn naam en bedrijf in te vullen.
4. Na invullen wordt er een `Visitor` + `Visit` aangemaakt (status `planned`).
5. Een token is **eenmalig** bruikbaar of verloopt na de bezoekdatum.

## Uitbreiding (goed)

- De uitnodiging wordt **per e-mail** verstuurd (sluit aan op de bestaande mail-functionaliteit, zie `VisitController::sendMail`).
- Tonen van een **bevestigingspagina** ("Bedankt, je bezoek is geregistreerd").
- De medewerker ziet de **status** van de uitnodiging (verstuurd / ingevuld / verlopen).

## Expert-uitdaging (excellent)

- Bezoeker kan de uitnodiging **annuleren** via dezelfde link.
- **Herinneringsmail** een dag van tevoren (combineerbaar met scheduling, zie opdracht 6).

## Technische hints

- Voeg een kolom toe via een **nieuwe migratie**: `php artisan make:migration add_invitation_token_to_visits_table`.
- Genereer een token met `Str::uuid()` of `Str::random(40)`.
- De publieke route staat **buiten** de `auth`-middleware. Gebruik **route-model-binding op het token**:
  ```php
  Route::get('/uitnodiging/{token}', [InvitationController::class, 'show'])->name('invitations.show');
  ```
- Beveilig tegen misbruik: valideer het formulier met een **Form Request** en rate-limit de publieke route.
- Denk aan **autorisatie**: alleen de medewerker die de uitnodiging maakte (of admin) mag de status zien.

## Testen

- Geldig token → formulier laadt (200). Ongeldig/verlopen token → 404 of nette foutpagina.
- Na versturen formulier → er bestaat een nieuwe `Visit` met status `planned`.
- Token kan **niet** twee keer gebruikt worden.

---

# Opdracht 4 — Geheimhoudingsverklaring (NDA) bij check-in ⭐⭐

## Scenario

Het bedrijf werkt met gevoelige informatie. Voortaan moet **elke bezoeker** bij binnenkomst
akkoord gaan met een **geheimhoudingsverklaring** (NDA). De receptie moet later kunnen aantonen
*dat* en *wanneer* een bezoeker akkoord ging.

## User story

**Als** beveiligings-/receptiemedewerker
**wil ik** dat een bezoeker bij het inchecken akkoord moet gaan met de geheimhoudingsverklaring
**zodat** we juridisch kunnen aantonen dat de bezoeker de voorwaarden heeft geaccepteerd.

## Acceptatiecriteria — Basis

1. Bij het inchecken wordt de **tekst van de geheimhoudingsverklaring** getoond.
2. Inchecken kan **alleen** als het vinkje "Ik ga akkoord" is aangezet (validatie).
3. Bij akkoord worden **opgeslagen**: dat er akkoord is (`true`) en het **tijdstip** van akkoord.
4. Op de bezoek-detailpagina is zichtbaar of/wanneer akkoord is gegaan.

## Uitbreiding (goed)

- Een **handtekening-veld** (typen van de naam ter bevestiging) dat moet matchen met de bezoekersnaam.
- De NDA-tekst staat in een **beheerbare** plek (config of database) i.p.v. hardcoded.

## Expert-uitdaging (excellent)

- **Versiebeheer** van de NDA: leg vast met wélke versie van de tekst de bezoeker akkoord ging.
- Een **digitale handtekening** tekenen op een `<canvas>` en als afbeelding opslaan.

## Technische hints

- Nieuwe migratie: kolommen `nda_accepted_at` (timestamp, nullable) op `visits`.
- Pas de bestaande `checkIn()`-methode in `VisitController` aan: voeg validatie toe (`accepted` rule)
  vóórdat `check_in_time` wordt gezet.
- Toon de status met een nette badge in `Visits/show.blade.php`.
- Houd het **AVG-bewust**: leg uit waarom je een tijdstip vastlegt en niet meer dan nodig.

## Testen

- Inchecken **zonder** akkoord → validatiefout, geen `check_in_time`.
- Inchecken **met** akkoord → `check_in_time` én `nda_accepted_at` gevuld.

---

# Opdracht 5 — Dashboard-grafiek "bezoeken per dag" ⭐⭐

## Scenario

Het management wil in één oogopslag de **drukte** zien: hoeveel bezoekers er de afgelopen periode
per dag waren. Het dashboard toont nu alleen losse getallen (tegeltjes), geen trend.

## User story

**Als** manager
**wil ik** op het dashboard een grafiek zien met het aantal bezoeken per dag van de afgelopen 7 dagen
**zodat** ik pieken en rustige dagen in de bezoekersdrukte kan herkennen.

## Acceptatiecriteria — Basis

1. Op het dashboard staat een **lijn- of staafdiagram** met het aantal bezoeken per dag (laatste 7 dagen).
2. De x-as toont de **datums**, de y-as het **aantal bezoeken**.
3. De data komt uit de database (op basis van `expected_arrival_time` of `check_in_time`).

## Uitbreiding (goed)

- Schakelaar tussen **7 / 30 dagen**.
- Tweede grafiek: bezoeken **per afdeling** (taart-/staafdiagram).

## Expert-uitdaging (excellent)

- Data via een **JSON-endpoint** ophalen (kleine eigen API) i.p.v. direct in de Blade.
- Grafiek **automatisch verversen** zonder pagina-herlaad.

## Technische hints

- De `DashboardController` levert al statistieken aan; breid de `index()` uit met een dag-array.
- Groeperen per dag kan met een query:
  ```php
  Visit::selectRaw('DATE(expected_arrival_time) as dag, COUNT(*) as aantal')
      ->where('expected_arrival_time', '>=', now()->subDays(7))
      ->groupBy('dag')->orderBy('dag')->get();
  ```
- Voor de grafiek: **Chart.js** via CDN of npm. Geef de data mee als JSON aan een `<canvas>`.
- Let op **lege dagen**: vul dagen zonder bezoeken aan met 0, anders ontbreken ze in de grafiek.

## Testen

- Test dat `DashboardController@index` de juiste dataset met de juiste aantallen teruggeeft
  (feature-test op de variabele/JSON, niet op de grafiek zelf).

---

# Opdracht 6 — No-show signalering ⭐⭐⭐

## Scenario

Soms maakt een medewerker een bezoek aan, maar **komt de bezoeker niet opdagen** (no-show).
Die geplande bezoeken blijven nu eindeloos als "gepland" in het systeem staan en vervuilen de
overzichten. Het bedrijf wil ze automatisch herkennen.

## User story

**Als** medewerker
**wil ik** dat geplande bezoeken die ruim na de verwachte aankomsttijd niet zijn ingecheckt,
gemarkeerd worden als **"no-show"**
**zodat** ik echte planning kan onderscheiden van bezoekers die niet kwamen.

## Acceptatiecriteria — Basis

1. Een geplande visit (geen `check_in_time`) waarvan de verwachte aankomst **meer dan X uur**
   geleden is, krijgt de afgeleide status **"no-show"**.
2. In het bezoekenoverzicht is "no-show" duidelijk zichtbaar (badge/kleur).
3. Er is een **filter** om alleen no-shows te tonen (sluit aan op het bestaande statusfilter).

## Uitbreiding (goed)

- Een **scheduled command** (`php artisan`) die dagelijks no-shows logt of een notificatie naar de host stuurt.
- Teller op het **dashboard**: "No-shows deze week: X".

## Expert-uitdaging (excellent)

- Host krijgt een **notificatie** (bestaand `Notification`-model) als zijn bezoeker een no-show wordt.
- No-show-percentage per afdeling in een rapport.

## Technische hints

- Begin met afgeleide logica in het model: breid `currentStatus()` in `Visit.php` uit met een
  no-show-tak (bijv. `planned` + `expected_arrival_time < now()->subHours(2)` → `no_show`).
- Voor automatisering: maak een command `php artisan make:command FlagNoShows` en plan deze in
  `routes/console.php` met de scheduler.
- Hergebruik het bestaande **notificatiepatroon** uit `VisitController::checkIn()`.

## Testen

- Visit met aankomst 3 uur geleden en geen check-in → status `no_show`.
- Visit met aankomst over 1 uur → status `planned` (géén no-show).
- Ingecheckt bezoek → nooit `no_show`.

---

# Opdracht 7 — Bezoeker-blacklist (toegangsweigering) ⭐⭐

## Scenario

Een enkele keer is er een persoon die **niet welkom** is in het pand (bijv. na een incident).
De receptie moet bij het aanmaken of inchecken van een bezoek **gewaarschuwd** worden als de
bezoeker op een zwarte lijst staat.

## User story

**Als** beveiligings-/receptiemedewerker
**wil ik** bezoekers op een blacklist kunnen zetten en bij het inchecken een duidelijke waarschuwing krijgen
**zodat** ongewenste personen geen toegang krijgen tot het pand.

## Acceptatiecriteria — Basis

1. Een **admin** kan een bezoeker op de blacklist zetten/halen, met een **reden**.
2. Bij het **inchecken** van een geblokkeerde bezoeker verschijnt een **duidelijke waarschuwing** en wordt inchecken **geweigerd**.
3. Op de bezoeker-detailpagina is de blacklist-status zichtbaar.

## Uitbreiding (goed)

- Ook bij het **aanmaken** van een nieuw bezoek waarschuwen als de gekozen bezoeker geblokkeerd is.
- Een **overzichtspagina** met alle geblokkeerde bezoekers.

## Expert-uitdaging (excellent)

- Blokkade met een **einddatum** (tijdelijk geweigerd t/m datum).
- Een **logregel/notificatie** naar de beheerder wanneer een geblokkeerde bezoeker wordt geweigerd.

## Technische hints

- Nieuwe migratie: kolommen `is_blocked` (boolean) en `block_reason` (string, nullable) op `visitors`.
- Voeg in `checkIn()` (en eventueel `store()`) een controle toe **vóór** de update; gebruik
  `back()->with('error', ...)` zoals de bestaande "already checked in"-melding.
- Beperk beheer tot rol **`admin`** via de bestaande `check.role`-middleware.
- Overweeg een **policy** als de autorisatie complexer wordt.

## Testen

- Geblokkeerde bezoeker inchecken → geweigerd, geen `check_in_time`, foutmelding zichtbaar.
- Niet-geblokkeerde bezoeker → inchecken werkt gewoon.
- Een `employee` mag **niet** de blacklist beheren (403); een `admin` wel.

---

## Algemene beoordelingspunten (alle opdrachten)

Naast de functionele eisen let je bij elke opdracht op:

- **Conventies:** volgt de student de bestaande structuur (controllers, routes, views, naamgeving)?
- **Hergebruik:** bouwt de student voort op bestaande code (scopes, middleware, patronen) i.p.v. opnieuw uitvinden?
- **Autorisatie:** kloppen de rollen (`check.role`) bij elke nieuwe route?
- **Testen:** is er minimaal één betekenisvolle Pest-test die slaagt?
- **Codestijl:** is `vendor/bin/pint --dirty` gedraaid?
- **Verantwoording:** kan de student in een code review uitleggen *waarom* keuzes zijn gemaakt?
- **AVG-besef** (waar relevant): worden alleen noodzakelijke persoonsgegevens vastgelegd?
