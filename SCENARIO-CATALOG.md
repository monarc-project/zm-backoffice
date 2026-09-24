# Scenario catalog API

`zm-backoffice` owns the authoring API for the global Scenario Library catalog.
The additive Phinx migration is in `zm-core/migrations/db` and creates the
versioned event definitions, templates, translations, template/event
composition, tags, references and append-only audit records in `monarc_common`.
`Monarc\Core\Scenario\Table\CatalogTable` is the sole database access boundary
for those shared tables. This lets a later FrontOffice reader use published
catalog versions without copying the catalog into client databases.

Catalog `author_id`, `reviewer_id` and audit `actor_id` values are BackOffice
identity references. They intentionally have no foreign key because BackOffice
users reside in `monarc_master`, while the shared catalog resides in
`monarc_common`.

The API is defined in `zm-core/openapi/scenario-v1.yaml`. Consumers regenerate
their client from that canonical document after an API change. Draft and review
content is limited to BackOffice catalog roles; only published/deprecated
content can be listed by a non-authoring catalog consumer. Mutations require an
`If-Match` revision. Publishing is terminal for that version; a later edit
creates the next draft version.

Use `tests/Fixture/ScenarioCatalogFixture.php` only in tests. It intentionally
contains generic content and is not a pilot template release.
