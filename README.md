## IPsec VPN Controll

Управление и мониториг VPN сервера

### Настройки

##### Sudoers

В качестве управления и мониторинга используется приложение `ipsec`, для доступа к нему нееобходимо, чтобы `php` был запущен от имени пользователя, входящего в группу `sudo`

Для разрешения выполнения sudo команд без ввода пароля, необходимо добавить строку в файл `/etc/sudoers`

```sh
# Allow members of group sudo to execute any command
%sudo	ALL=(ALL:ALL) ALL
username ALL=(ALL) NOPASSWD: /usr/sbin/ipsec
```
где `username` имя пользователя от которого запущен `php`

##### UpDown

Чтобы фиксировать подключение и отключение пользоватей VPN, необходимо изменить файл `/usr/lib/ipsec/_updown`

В начале добавьте переменную с расположением исполняющего файла artisan
```sh
# define a minimum PATH environment in case it is not set
PATH="/sbin:/bin:/usr/sbin:/usr/bin:/usr/sbin"
export PATH

# comment to disable logging VPN connections to syslog
VPN_LOGGING=1
#
# tag put in front of each log entry:
TAG=vpn
#
# syslog facility and priority used:
FAC_PRIO=local0.notice
#
# to create a special vpn logging file, put the following line into
# the syslog configuration file /etc/syslog.conf:
#
# local0.notice                   -/var/log/vpn
#
# IPsec vpn hook
VPN_HOOK="/var/www/vpn-conrol/artisan"
```

Затем найдите строку `case "$PLUTO_VERB:$1" in` и для кейсов
- `up-client:)`
- `down-client:)`
- `up-client-v6:)`
- `down-client-v6:)`

```sh
php $VPN_HOOK app:ipsec-updown $PLUTO_XAUTH_ID \
	--verb=$PLUTO_VERB \
	--connect=$PLUTO_CONNECTION \
	--uniqueid=$PLUTO_UNIQUEID \
	--reqid=$PLUTO_REQID \
	--peer=$PLUTO_PEER \
	--ip=$PLUTO_PEER_SOURCEIP
```

Также потребуется в настройках вашего подключения указать путь до скрипта `_updown`

`/etc/ipsec.conf`
```conf
    conn <name>
        ...
        leftupdown=/usr/lib/ipsec/_updown
        rightupdown=/usr/lib/ipsec/_updown
        ...
```

После чего перезапустите `ipsec`
```bash
ipsec restart
```
