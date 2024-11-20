erDiagram 

	%% 13-UsableSites
	UsableSites {
	  integer id PK "AUTO_INCREMENT"
		string(64) entity_type "NOT_NULL"
	  integer entity_id "NOT_NULL"
	  reference site_id "NOT_NULL"
	  boolean shared_login_allowed "default: false"
	  datetime created_at  "NOT_NULL"
	  datetime updated_at
	  datetime deleted_at

		%% entity_type: App\Models\Authenticates or App\Models\AuthenticateOauths: managed in application
		
		%% INDEX(COMPOSITE): idx_cp_usable_sites (entity_type, entity_id)
		%% INDEX: idx_fk_usable_sites_site_id (site_id)
	}

	%% 14-SiteAuthProviders
	SiteAuthProviders {
	  integer id PK "AUTO_INCREMENT"
	  reference site_id FK "NOT_NULL: Sites.id"
	  reference auth_provider_id FK "NOT_NULL: AuthProviders.id"
	  boolean is_enabled "default: true"
	  datetime created_at  "NOT_NULL"
	  datetime updated_at

	  %% INDEX(COMPOSITE): idx_cp_site_auth_providers (site_id, auth_provider_id)
	  %% INDEX: idx_fk_site_auth_providers_site_id (site_id)
	  %% INDEX: idx_fk_site_auth_providers_auth_provider_id (auth_provider_id)
	  %% INDEX(UNIQUE): idx_uq_site_auth_providers (site_id, auth_provider_id)
	}

	%% -----------------------------------
	%% オペレータ管理機能
	%% -----------------------------------
	Sites ||--o{ SiteOperators: "belong"
	SiteOperators ||--o{ SiteOperatorRoles: "belong"
	Companies ||--o{ Sites: "has"
	Companies ||--o{ Operators: "has"
	Operators ||--|| OperatorRanks: "has"
	Operators ||--o{ SiteOperators: "belong"

	%% 20-Operators
	Operators {
		integer id PK "AUTO_INCREMENT"
		string(64) operator_code UK "NOT_NULL"
		reference company_id FK "NOT_NULL: Companies.id"
		string(32) name "NOT_NULL"
		reference operator_rank_id FK "NOT_NULL: OperatorRanks.id"
		datetime created_at "NOT_NULL"
		datetime updated_at
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_operators_operator_code (operator_code)
		%% INDEX: idx_fk_operators_company_id (company_id)
		%% INDEX: idx_fk_operators_operator_rank_id (operator_rank_id)
		%% INDEX(FULLTEXT): idx_ft_operators_name (name)
	}

	%% 21-SiteOperators
	SiteOperators {
		integer id PK "AUTO_INCREMENT"
		reference site_id FK "NOT_NULL: Sites.id"
		reference operator_id FK "NOT_NULL: Operators.id"
		reference role_id FK "NOT_NULL: SiteOperatorRoles.id"
		datetime created_at
		datetime updated_at
		datetime deleted_at
		
		%% INDEX(COMPOSITE): idx_cp_site_operators (site_id, operator_id, role_id)
		%% INDEX(UNIQUE): idx_uq_site_operators (site_id, operator_id, role_id)
	}

	%% 22-OperatorRanks
	OperatorRanks {
		integer id PK "AUTO_INCREMENT"
		string(32) name "NOT_NULL"
		integer priority "NOT_NULL: default: 1"
		datetime created_at "NOT_NULL"
		datetime updated_at
		datetime deleted_at

		%% INDEX: idx_col_operator_ranks_name (name)
	}

	%% 23-SiteOperatorRoles
	SiteOperatorRoles {
		integer id PK "AUTO_INCREMENT"
		string(32) name "NOT_NULL"
		datetime created_at "NOT_NULL"
		datetime updated_at
		datetime deleted_at

		%% INDEX: idx_col_site_operator_roles_name (name)
	}

	%% 24-Companies
	Companies {
		integer id PK "AUTO_INCREMENT"
		string(64) company_code UK "NOT_NULL"
		string(32) company_name "NOT_NULL"
		string(64) name "NOT_NULL"
		string(10) postal_code
		string(128) address
		string(24) phone
		string(24) phone2
		string(24) fax
		datetime created_at
		datetime updated_at
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_companies_company_code (company_code)
		%% INDEX: idx_col_companies_company_name (company_name)
		%% INDEX: idx_col_companies_name (name)
		%% INDEX: idx_col_companies_postal_code (postal_code)
		%% INDEX(FULLTEXT): idx_col_companies_address (address)
		%% INDEX: idx_col_companies_tel (tel)
	}

	%% 25-Sites
	Sites {
		integer id PK "AUTO_INCREMENT"
		string(64) site_code UK "NOT_NULL"
		reference company_id FK "NOT_NULL: Companies.id"
		string(255) url UK "NOT_NULL"
		string(64) name "NOT_NULL"
		string(255) description
		boolean is_btob "default: false"
		datetime created_at "NOT_NULL"
		datetime updated_at
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_sites_site_code (site_code)
		%% INDEX(UNIQUE): idx_uq_sites_url (url)
		%% INDEX: idx_col_sites_name (name)
		%% INDEX(FULLTEXT): idx_col_sites_description (description)
	}

	%% -----------------------------------
	%% お知らせ機能
	%% -----------------------------------
	Notifications ||--o{ NotificationReceivers : "管理"
	Notifications ||--o{ NotificationCategories: "belongs to"
	Notifications ||--o{ NotificationSenders : "送信者"
	Users ||--o{ NotificationReceivers : "対象"
	Operators ||--o{ NotificationReceivers : "対象"
	Companies ||--o{ NotificationReceivers : "対象"
	Sites ||--o{ NotificationReceivers : "対象"
	NotificationReceivers ||--o{ NotificationSendMethods : "belongs to"
	NotificationCategories ||--o{ NotificationCategories: "parent-child"
	
	%% 30-Notifications
	Notifications {
		integer id PK "AUTO_INCREMENT"
		string(64) notification_code UK "NOT_NULL"
		reference category_id FK "NOT_NULL: NotificationCategories.id"
		string(64) title "NOT_NULL"
		text content "NOT_NULL"
		datetime publish_start_at "NOT_NULL"
		datetime publish_end_at
		datetime created_at
		datetime updated_at
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_notifications_notification_code (notification_code)	
		%% INDEX: idx_col_notifications_category_id (category_id)	
		%% INDEX: idx_col_notifications_publish_start_at (publish_start_at)
		%% INDEX: idx_col_notifications_publish_end_at (publish_end_at)
		%% INDEX: idx_col_notifications_created_at (created_at)
	}

	%% 31-NotificationCategories
	NotificationCategories {
		integer id PK "AUTO_INCREMENT"
		string(64) name "NOT_NULL"
		string(255) description
		reference parent_id FK "NULL: NotificationCategories.id"
		datetime created_at
		datetime updated_at
		datetime deleted_at

		%% INDEX: idx_col_notification_categories_name (name)
		%% INDEX: idx_fk_notification_categories_parent_id (parent_id)
	}

	%% 32-NotificationReceivers
	NotificationReceivers {
		integer id PK "AUTO_INCREMENT"
		reference notification_id FK "NOT_NULL" 
		string(64) entity_type "NOT_NULL"
		integer entity_id FK "NOT_NULL"
		reference send_method_id FK "NOT_NULL: NotificationSendMethods.id"
		datetime sent_at "NOT_NULL"
		boolean is_read "Mark as read status: default: false"
		datetime read_at "Mark as read date and time"
		datetime created_at "NOT_NULL"
		datetime deleted_at

		%% entity_type: App\Models\Companies or App\Models\Operators or App\Models\Users
		
		%% INDEX(COMPOSITE): idx_cp_notification_receivers (entity_type, entity_id)
		%% INDEX: idx_fk_notification_receivers_notification_id (notification_id)
		%% INDEX: idx_col_notification_receivers_send_method (send_method)
		%% INDEX: idx_col_notification_receivers_sent_at (sent_at)
		%% INDEX: idx_col_notification_receivers_checked_at (checked_at)
	}

	%% 33-NotificationSenders
	NotificationSenders {
		integer id PK "AUTO_INCREMENT"
		reference notification_id FK "NOT_NULL"
		string(64) entity_type "NOT_NULL"
		integer entity_id FK "NOT_NULL"
		datetime created_at "NOT_NULL"
		datetime deleted_at

		%% entity_type: App\Models\Companies or App\Models\Sites or App\Models\Operators or App\Models\Users
		
		%% INDEX(COMPOSITE): idx_cp_notification_senders (entity_type, entity_id)
		%% INDEX: idx_fk_notification_senders_notification_id (notification_id)
	}

	%% 34-NotificationSendMethods
	NotificationSendMethods {
		integer id PK "AUTO_INCREMENT"
		string(64) name "NOT_NULL"
		string(255) description
		datetime created_at "NOT_NULL"
		datetime updated_at
		datetime deleted_at
	}

	%% -----------------------------------
	%% 外部システム連携機能
	%% -----------------------------------
	Users ||--o{ UserExternalCodes : "対象"

	%% 40-UserExternalCodes
	UserExternalCodes {
		integer id PK "AUTO_INCREMENT"
		reference user_id FK "NOT_NULL"
		string(255) external_code "NOT_NULL"
		datetime created_at
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_user_external_codes (user_id, external_code)
		%% INDEX: idx_fk_user_external_codes_user_id (user_id)
		%% INDEX: idx_col_user_external_codes_external_code (external_code)
		%% INDEX: idx_col_user_external_codes_deleted_at (created_at)
		%% INDEX: idx_col_user_external_codes_deleted_at (deleted_at)
	}

	%% -----------------------------------
	%% お気に入り商品管理機能
	%% -----------------------------------
	Sites ||--o{ FavoriteItems : "belong"
	Users ||--o{ FavoriteItems : "belong"
	Items ||--o{ FavoriteItems : "belong"  

	%% 50-FavoriteItems
	FavoriteItems {
		integer id PK "AUTO_INCREMENT"
		reference user_id FK "NOT_NULL" 
		reference item_id FK "NOT_NULL"
		reference site_id FK "NOT_NULL"
		datetime created_at "NOT_NULL"
		datetime deleted_at

		%% INDEX(COMPOSITE): idx_cp_favorite_items (user_id, item_id, site_id)
		%% INDEX(UNIQUE): idx_uq_favorite_items (user_id, item_id, site_id)
		%% INDEX: idx_col_favorite_items_deleted_at (deleted_at)
	}

	%% -----------------------------------
	%% 商品管理機能
	%% -----------------------------------
	Sites ||--o{ Items : "belong"
	Items ||--o{ ItemCategories : "belong"
	Items ||--o{ ItemUnits : "belong"

	%% 60-Items
	Items {
		integer id PK "AUTO_INCREMENT"
		string(64) item_code UK "NOT_NULL"
		reference site_id FK "NOT_NULL"
		reference category_id FK "NOT_NULL"
		string(64) maker_name
		string(64) name "NOT_NULL"
		text description
		decimal unit_price "NOT_NULL: (10,2)"
		reference unit_id FK "NOT_NULL"
		enum from_source "NOT_NULL: MANUAL, IMPORT"
		boolean is_recommended "default: false"
		datetime published_at
		datetime created_at "NOT_NULL"
		datetime updated_at
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_items_item_code (item_code)
		%% INDEX(FULLTEXT): idx_col_items_maker_name (maker_name)
		%% INDEX(FULLTEXT): idx_col_items_name (name)
		%% INDEX(FULLTEXT): idx_col_items_description (description)
		%% INDEX: idx_fk_items_site_id (site_id)
		%% INDEX: idx_fk_items_category_id (category_id)
	}
	
	%% 61-ItemCategories
	ItemCategories {
		integer id PK "AUTO_INCREMENT"
		string(64) category_code UK "NOT_NULL"
		reference site_id FK "NOT_NULL"
		string(64) name "NOT_NULL" 
		integer priority "NOT_NULL: default: 1"
		boolean is_published "default: false"
		datetime created_at "NOT_NULL"
		datetime updated_at
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_item_categories_category_code (category_code)
		%% INDEX: idx_fk_item_categories_site_id (site_id)
		%% INDEX: idx_uq_item_categories_name (name)
	}
	
	%% 62-ItemUnits
	ItemUnits {
		integer id PK "AUTO_INCREMENT"
		string(64) unit_code UK "NOT_NULL"
		reference site_id FK "NOT_NULL"
		string(64) name "NOT_NULL" 
		integer priority "NOT_NULL: default: 1"
		boolean is_published "default: false"
		datetime created_at
		datetime updated_at
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_item_units_unit_code (unit_code)
		%% INDEX: idx_fk_item_units_site_id (site_id)
		%% INDEX: idx_uq_item_units_name (name)
	}

	%% -----------------------------------
	%% 注文管理機能
	%% -----------------------------------
	Sites ||--o{ Orders : "belong"
	Users ||--o{ Orders : "belong"
	Orders ||--o{ OrderDetails : "belong"
	OrderDetails ||--|| Items : "has"

	%% 70-Orders
	Orders {
		integer id PK "AUTO_INCREMENT"
		string(64) order_code UK "NOT_NULL" 
		reference site_id FK "NOT_NULL"
		reference user_id FK "NOT_NULL"
		decimal total_price "(10,2)"
		decimal tax "(8,0)"
		decimal postage "(4,0)"
		datetime ordered_at
		datetime processed_at
		datetime exported_at
		datetime created_at "NOT_NULL"
		datetime updated_at
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_orders_order_code (order_code)
		%% INDEX(COMPOSITE): idx_cp_orders (site_id, user_id)
		%% INDEX: idx_col_ordered_at (ordered_at)
		%% INDEX: idx_col_processed_at (processed_at)
	}

	%% 71-OrderDetails
	OrderDetails {
		integer id PK "AUTO_INCREMENT"
		string(64) detail_code UK "NOT_NULL"
		reference order_id FK "NOT_NULL"
		reference item_id FK "NOT_NULL"
		integer volume "NOT_NULL"
		decimal unit_price "NOT_NULL: (10,2)"
		string(64) unit_name "NOT_NULL"
		decimal price "NOT_NULL: (10,2)"
		decimal tax "NOT_NULL: (4,0)"
		datetime processed_at
		datetime created_at
		datetime updated_at
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_order_details_detail_code (detail_code)
		%% INDEX(COMPOSITE): idx_cp_order_details (order_id, item_id)
		%% INDEX(FULLTEXT): idx_cp_order_details_unit_name (unit_name)
	}

	%% -----------------------------------
	%% 顧客管理機能
	%% -----------------------------------
	Sites ||--o{ Users : "belong"

	%% 80-Users
	Users {
	  integer id PK "AUTO_INCREMENT"
	  string(64) user_code UK "NOT_NULL"
	  reference site_id FK "NOT_NULL: Sites.id"
	  string(64) name "NOT_NULL"
	  string(10) postal_code
	  string(128) address
	  string(24) phone
	  string(24) phone2
	  string(24) fax
	  datetime created_at
	  datetime updated_at
	  datetime deleted_at

	  %% INDEX(UNIQUE): idx_uq_users_user_code (user_code)
	  %% INDEX: idx_fk_users_site_id (site_id)
	  %% INDEX: idx_col_users_name (name)
	}

	%% -----------------------------------
	%% セキュリティログ機能
	%% -----------------------------------
	Sites ||--o{ SecurityLogs : "belong"
	
	%% SecurityLogs
	SecurityLogs {
		integer id PK "AUTO_INCREMENT"
		string(64) entity_type "NOT_NULL: User, Operator, Company, Admin"
		integer entity_id "NOT_NULL"
		string(32) action_type "NOT_NULL: LOGIN, LOGOUT, DATA_ACCESS, DATA_CREATE, DATA_UPDATE, DATA_DELETE"
		string(255) action_detail
		string(39) ip_address "NOT_NULL"
		text user_agent
		datetime created_at "NOT_NULL"

		%% INDEX(COMPOSITE): idx_cp_security_logs (entity_type, entity_id)
		%% INDEX: idx_col_security_logs_action_type (action_type)
		%% INDEX: idx_col_security_logs_created_at (created_at)
	}

	%% -----------------------------------
	%% メッセージログ機能
	%% -----------------------------------
	Sites ||--o{ MessageLogs : "belong"
	Operators ||--o{ MessageLogs : "send"
	Users ||--o{ MessageLogs : "receive"

	%% MessageLogs
	MessageLogs {
		integer id PK "AUTO_INCREMENT"
		string(64) message_code UK "NOT_NULL"
		reference site_id FK "NOT_NULL: Sites.id"
		reference operator_id FK "NOT_NULL: Operators.id"
		reference user_id FK "NOT_NULL: Users.id"
		string(32) message_type "NOT_NULL: LINE等"
		text message_content "NOT_NULL"
		datetime sent_at "NOT_NULL"
		datetime created_at "NOT_NULL"
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_message_logs_message_code (message_code)
		%% INDEX: idx_col_message_logs_message_type (message_type)
		%% INDEX: idx_col_message_logs_sent_at (sent_at)
		%% INDEX(COMPOSITE): idx_cp_message_logs_site_operator (site_id, operator_id)
		%% INDEX(COMPOSITE): idx_cp_message_logs_site_user (site_id, user_id)
	}

	%% -----------------------------------
	%% インポート/エクスポートログ機能
	%% -----------------------------------
	Sites ||--o{ ImportExportLogs : "belong"
	Operators ||--o{ ImportExportLogs : "execute"

	%% ImportExportLogs
	ImportExportLogs {
		integer id PK "AUTO_INCREMENT"
		string(64) log_code UK "NOT_NULL"
		reference site_id FK "NOT_NULL: Sites.id"
		reference operator_id FK "NOT_NULL: Operators.id"
		string(32) operation_type "NOT_NULL: IMPORT, EXPORT"
		string(32) target_type "NOT_NULL: USER, ITEM, ORDER"
		string(255) file_name "NOT_NULL"
		integer record_count "NOT_NULL"
		string(32) status "NOT_NULL: SUCCESS, FAILED"
		text error_detail
		datetime created_at "NOT_NULL"
		datetime deleted_at

		%% INDEX(UNIQUE): idx_uq_import_export_logs_log_code (log_code)
		%% INDEX(COMPOSITE): idx_cp_import_export_logs_site_operator (site_id, operator_id)
		%% INDEX: idx_col_import_export_logs_operation_type (operation_type)
		%% INDEX: idx_col_import_export_logs_target_type (target_type)
		%% INDEX: idx_col_import_export_logs_status (status)
		%% INDEX: idx_col_import_export_logs_created_at (created_at)
	}

	%% -----------------------------------
	%% パスワード履歴機能
	%% -----------------------------------
	PasswordHistories {
		integer id PK "AUTO_INCREMENT"
		reference authenticate_id FK "NOT_NULL: Authenticates.id"
		string password "NOT_NULL"
		datetime created_at "NOT_NULL"

		%% INDEX: idx_fk_password_histories_authenticate_id (authenticate_id)
		%% INDEX: idx_col_password_histories_created_at (created_at)
	}

	%% -----------------------------------
	%% ログイン試行機能
	%% -----------------------------------
	LoginAttempts {
		integer id PK "AUTO_INCREMENT"
		string(64) login_id "NOT_NULL"
		string(39) ip_address "NOT_NULL"
		boolean is_success "NOT_NULL, DEFAULT: false"
		text failure_reason
		datetime created_at "NOT_NULL"

		%% INDEX: idx_col_login_attempts_login_id (login_id)
		%% INDEX: idx_col_login_attempts_ip_address (ip_address)
		%% INDEX: idx_col_login_attempts_created_at (created_at)
	}

	%% -----------------------------------
	%% インポートタスク管理機能
	%% -----------------------------------
	Sites ||--o{ ImportTasks : "関連"
	ImportTasks {
		integer id PK "AUTO_INCREMENT"
		string task_code "NOT_NULL"
		reference site_id FK "Sites.id"
		string data_type "NOT_NULL"
		string file_path "NOT_NULL"
		string status "NOT_NULL"
		string status_message "nullable"
		string imported_by "NOT_NULL"
		datetime uploaded_at "NOT_NULL"
		datetime imported_at "nullable"
	}
