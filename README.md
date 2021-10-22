# Digger

[![Terminus v1.x Compatible](https://img.shields.io/badge/terminus-v1.x-green.svg)](https://github.com/pantheon-systems/terminus-dig/tree/1.x)

A Terminus plugin for inspecting DNS records.

## Examples

To get the A record (default):
```
terminus dig <site>.<env>
```

To get the AAAA records:
```
terminus dig <site>.<env> --type=AAAA
```

To get the CNAME:
```
terminus dig <site>.<env> --type=CNAME
```