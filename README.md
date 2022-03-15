# Digger

[![Terminus v1.x Compatible](https://img.shields.io/badge/terminus-v1.x-green.svg)](https://github.com/pantheon-systems/terminus-dig/tree/1.x)

A Terminus plugin for inspecting DNS records.

## For appservers or dbservers

To get the appserver IP address:
```
terminus dig:server <site>.<env>
```

To get the dbserver IP address:
```
terminus dig:server <site>.<env> --server=dbserver
```

To get the first IP address only:
```
terminus dig:server <site>.<env> | head -n 1
```

## For domains

To get the A record (default):
```
terminus dig:domain --domain=pantheon.io
```

To get the AAAA records:
```
terminus dig:domain --domain=domain.com --type=AAAA
```

To get the custom domain CNAME:
```
terminus dig:domain --domain=google.com --type=CNAME
```

To get the custom domain MX:
```
terminus dig:domain --domain=google.com --type=MX
```

## Installation

For Terminus 2 and below, you can install like this:
```
$ cd ~/.terminus/plugins
$ git clone https://github.com/pantheon-systems/terminus-dig.git
```
For Terminus 3, you can install like this:
```
$ terminus self:plugin:install geraldvillorente/terminus-dig
```
Or like this:
```
$ git clone https://github.com/pantheon-systems/terminus-dig.git
$ terminus self:plugin:install terminus-dig
```