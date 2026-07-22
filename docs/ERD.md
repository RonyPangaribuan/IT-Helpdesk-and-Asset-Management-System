# Entity Relationship Diagram

This ERD follows the actual Laravel migrations in this repository.

```mermaid
erDiagram
    users {
        bigint id PK
        string name
        string email UK
        timestamp email_verified_at
        string password
        string role
        string phone
        boolean is_active
        string remember_token
        timestamp created_at
        timestamp updated_at
    }

    ticket_categories {
        bigint id PK
        string name UK
        text description
        boolean is_active
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    asset_categories {
        bigint id PK
        string name UK
        text description
        boolean is_active
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    assets {
        bigint id PK
        string asset_code UK
        string name
        bigint asset_category_id FK
        string brand
        string model
        string serial_number UK
        string location
        string condition
        text description
        boolean is_active
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    tickets {
        bigint id PK
        string ticket_code UK
        bigint requester_id FK
        bigint technician_id FK "nullable"
        bigint ticket_category_id FK
        bigint asset_id FK "nullable"
        string title
        text description
        string location
        string priority
        string status
        text resolution_note
        timestamp resolved_at
        timestamp closed_at
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    ticket_comments {
        bigint id PK
        bigint ticket_id FK
        bigint user_id FK
        text body
        timestamp created_at
        timestamp updated_at
        timestamp deleted_at
    }

    ticket_attachments {
        bigint id PK
        bigint ticket_id FK
        bigint uploaded_by FK
        string original_name
        string stored_name
        string file_path
        string mime_type
        bigint file_size
        timestamp created_at
        timestamp updated_at
    }

    ticket_status_histories {
        bigint id PK
        bigint ticket_id FK
        bigint changed_by FK
        string old_status
        string new_status
        text note
        timestamp created_at
    }

    sessions {
        string id PK
        bigint user_id FK "nullable"
        string ip_address
        text user_agent
        longtext payload
        integer last_activity
    }

    jobs {
        bigint id PK
        string queue
        longtext payload
        tinyint attempts
        integer reserved_at
        integer available_at
        integer created_at
    }

    job_batches {
        string id PK
        string name
        integer total_jobs
        integer pending_jobs
        integer failed_jobs
        longtext failed_job_ids
        mediumtext options
        integer cancelled_at
        integer created_at
        integer finished_at
    }

    failed_jobs {
        bigint id PK
        string uuid UK
        text connection
        text queue
        longtext payload
        longtext exception
        timestamp failed_at
    }

    users ||--o{ tickets : requester_id
    users ||--o{ tickets : technician_id
    users ||--o{ ticket_comments : user_id
    users ||--o{ ticket_attachments : uploaded_by
    users ||--o{ ticket_status_histories : changed_by
    users ||--o{ sessions : user_id
    ticket_categories ||--o{ tickets : ticket_category_id
    asset_categories ||--o{ assets : asset_category_id
    assets ||--o{ tickets : asset_id
    tickets ||--o{ ticket_comments : ticket_id
    tickets ||--o{ ticket_attachments : ticket_id
    tickets ||--o{ ticket_status_histories : ticket_id
```

Notes:

- `tickets.technician_id` is nullable until an administrator assigns a technician.
- `tickets.asset_id` is nullable because tickets can be created without a related asset.
- Ticket, category, comment, and asset archive behavior uses soft deletes where implemented.
- Attachments are private files referenced by database metadata and served through authorization.
