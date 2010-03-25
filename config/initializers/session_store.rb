# Be sure to restart your server when you modify this file.

# Your secret key for verifying cookie session data integrity.
# If you change this key, all old sessions will become invalid!
# Make sure the secret is at least 30 characters and all random, 
# no regular words or you'll be exposed to dictionary attacks.
ActionController::Base.session = {
  :key         => '_holey-moley_session',
  :secret      => '2fa39ffe1409b6f4d4e0102318743944ea99bcddb333152992cf72a996a5b478ab05886f5201b20db696ae9cf6e97a697acdcf26c4dd82d1b5eb9751b37a8bf8'
}

# Use the database for sessions instead of the cookie-based default,
# which shouldn't be used to store highly confidential information
# (create the session table with "rake db:sessions:create")
# ActionController::Base.session_store = :active_record_store
