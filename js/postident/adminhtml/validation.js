Validation.add('validate-clientsecret', 'Only charakters a-z, A-Z, 0-9 are allowed.',
    function(v) {
        return /^[a-zA-Z0-9]+$/.test(v);
    }
 );

Validation.add('validate-clientid', 'Please Enter a valid Client-ID.',
    function(v) {
       return /^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/.test(v);
    }
 );