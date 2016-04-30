lobby.crt file's contents is merged to cacert.pem

Ultimately, cacert.pem is used by cURL

For this, copy the cacert.pem file to :
```
includes/src/vendor/rmccue/requests/library/Requests/Transport
```
