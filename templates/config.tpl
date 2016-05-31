[database]
adapter      = Mysql
host         = {{db_host}}
username     = {{db_username}}
password     = {{db_password}}
dbname       = {{db_name}}
charset      = utf8

[mail]
host         = {{ma_host}}
username     = {{ma_username}}
password     = {{ma_password}}
security     = {{ma_security}}
port         = {{ma_port}}
charset      = UTF-8
email        = {{ma_email}}
name         = {{ma_name}}

[permissions]
active      = true
user        = 0
client      = 1
team        = 2
admin       = 3
dev         = 4

[logs]
create      = 1
update      = 2
delete      = 3
info        = 4

[debug]
active      = true
