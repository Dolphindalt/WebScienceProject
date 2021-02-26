# Obviously, this is only for the development env.
sudo mysql -u root < create_tables.sql
sudo mysql -u root < views.sql
sudo mysql -u root < stored_procs.sql