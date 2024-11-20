erDiagram 
	%% -----------------------------------
	%% 認証機能
	%% -----------------------------------
	AuthenticateOauths ||--|| AuthProviders: "has"
	AuthenticateOauths ||--|| UsableSites : "has"
	Authenticates ||--|| UsableSites : "has"
	Users ||--o{ Authenticates: "has"
	Users ||--o{ AuthenticateOauths: "has"
	Sites ||--o{ AuthenticateOauths: "has"
	Sites ||--o{ Authenticates: "has"
	Sites ||--o{ UsableSites: "has"
	Sites ||--o{ SiteAuthProviders : "can use"
	AuthProviders ||--o{ SiteAuthProviders : "can be used by"
	
	%% 10-Authenticates
	Authenticates {
		integer id PK "AUTO_INCREMENT"
		string(64) auth_code UK "NOT_NULL"
		reference site_id FK "NOT_NULL: Sites.id" 
		string(64) entity_type "NOT_NULL"
		integer entity_id "NOT_NULL"
		string(50) login_code "NOT_NULL"
		string(255) password "NOT_NULL: hashed password using bcrypt"
		datetime expires_at "NOT_NULL"
		datetime created_at "NOT_NULL"
		datetime updated_at
		datetime deleted_at

		%% entity_type: App\Models\Companies or App\Models\Operators or App\Models\Users

		%% INDEX(UNIQUE): idx_uq_authenticates_auth_code (auth_code)
		%% INDEX(COMPOSITE): idx_cp_authenticates (entity_type, entity_id)
		%% INDEX: idx_fk_authenticates_site_id (site_id)
		%% INDEX: idx_col_authenticates_login_code (login_code)
		%% INDEX: idx_col_authenticates_password (password)
	}
		
	%% 11-AuthenticateOauths
	AuthenticateOauths {
		integer id PK "AUTO_INCREMENT"
		string(64) auth_code UK "NOT_NULL"
		reference site_id FK "NOT_NULL: Sites.id"
		string(64) entity_type "NOT_NULL"
		integer entity_id "NOT_NULL"
		reference auth_provider_id FK "NOT_NULL: AuthProviders.id"
		string(255) token "NOT_NULL: Use hashed token using encrypt function"
		datetime expires_at "NOT_NULL"
		datetime created_at "NOT_NULL"
		datetime updated_at
		datetime deleted_at

		%% entity_type: App\Models\Companies or App\Models\Operators or App\Models\Users

		%% INDEX(UNIQUE): idx_uq_authenticate_oauths_auth_code (auth_code)
		%% INDEX(COMPOSITE): idx_cp_authenticate_oauths (entity_type, entity_id)
		%% INDEX: idx_fk_authenticate_oauths_site_id (site_id)
		%% INDEX: idx_fk_auth_authenticate_oauths_provider_id (auth_provider_id)
	}

	%% 12-AuthProviders
	AuthProviders {
	  integer id PK "AUTO_INCREMENT"
		string(64) provider_code UK "NOT_NULL"
	  string(32) name "NOT_NULL"
	  string(255) description
	  boolean  is_enable "default: false"
		datetime created_at "NOT_NULL"
		datetime updated_at
	  datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_auth_providers_provider_code (provider_code)
		%% INDEX: idx_col_auth_providers_name (name)
	}
