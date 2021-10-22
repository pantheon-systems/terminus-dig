# Digger

[![Terminus v1.x Compatible](https://img.shields.io/badge/terminus-v1.x-green.svg)](https://github.com/pantheon-systems/terminus-dig/tree/1.x)

A Terminus plugin for inspecting DNS records.

## Examples

If `--domain` is not provided the script will default to appserver SFTP hostname.

To get the A record (default):
```
terminus dig <site>.<env>
```

To get the AAAA records:
```
terminus dig <site>.<env> --domain=domain.com --type=AAAA
```

To get the custom domain CNAME:
```
terminus dig <site>.<env> --domain=google.com --type=CNAME
```

To get the custom domain MX:
```
terminus dig <site>.<env> --domain=google.com --type=MX
```