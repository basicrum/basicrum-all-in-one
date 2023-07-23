CREATE TABLE IF NOT EXISTS webperf_rum_events (
  event_date                      Date DEFAULT toDate(created_at),
  hostname                        LowCardinality(String),
  created_at                      DateTime,
  event_type                      LowCardinality(String),
  browser_name                    LowCardinality(String),
  browser_version                 Nullable(String),
  ua_vnd                          LowCardinality(Nullable(String)),
  ua_plt                          LowCardinality(Nullable(String)),
  device_type                     LowCardinality(String),
  device_manufacturer             LowCardinality(Nullable(String)),
  operating_system                LowCardinality(String),
  operating_system_version        Nullable(String),
  user_agent                      Nullable(String),
  next_hop_protocol               LowCardinality(String),
  visibility_state                LowCardinality(String),

  session_id                      FixedString(43),
  session_length                  UInt8,
  url                             String,
  connect_duration                Nullable(UInt16),
  dns_duration                    Nullable(UInt16),
  first_byte_duration             Nullable(UInt16),
  redirect_duration               Nullable(UInt16),
  redirects_count                 UInt8,

  first_contentful_paint          Nullable(UInt16),
  first_paint                     Nullable(UInt16),

  cumulative_layout_shift         Nullable(Float32),
  first_input_delay               Nullable(UInt16),
  largest_contentful_paint        Nullable(UInt16),

  geo_country_code                FixedString(2),
  geo_city_name                   Nullable(String),
  page_id                         FixedString(8),

  data_saver_on                   Nullable(UInt8),

  boomerang_version               LowCardinality(String),
  screen_width                    Nullable(UInt16),
  screen_height                   Nullable(UInt16),

  dom_res                         Nullable(UInt16),
  dom_doms                        Nullable(UInt16),
  mem_total                       Nullable(UInt32),
  mem_limit                       Nullable(UInt32),
  mem_used                        Nullable(UInt32),
  mem_lsln                        Nullable(UInt32),
  mem_ssln                        Nullable(UInt32),
  mem_lssz                        Nullable(UInt32),
  scr_bpp                         Nullable(String),
  scr_orn                         Nullable(String),
  cpu_cnc                         Nullable(UInt8),
  dom_ln                          Nullable(UInt16),
  dom_sz                          Nullable(UInt16),
  dom_ck                          Nullable(UInt16),
  dom_img                         Nullable(UInt16),
  dom_img_uniq                    Nullable(UInt16),
  dom_script                      Nullable(UInt16),
  dom_iframe                      Nullable(UInt16),
  dom_link                        Nullable(UInt16),
  dom_link_css                    Nullable(UInt16)
)
ENGINE = MergeTree()
PARTITION BY toYYYYMMDD(event_date)
ORDER BY (device_type, event_date)
SETTINGS index_granularity = 8192