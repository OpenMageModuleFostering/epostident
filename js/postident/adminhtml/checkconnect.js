function sentCheckConnectRequest(url)
{
    url = url + '/clientId/' + $('postident_master_data_client_id').value + domainUri;
    request = new Ajax.Request(url, {
        onSuccess: function(transport) {
            if (transport.responseText.isJSON()) {
                var response = transport.responseText.evalJSON();
                if ('undefined' != typeof(response.message)) {
                    alert(response.message);
                } else {
                    alert(Translator.translate("Check Connect Error: Message not found."));
                }

            } else {
                alert(Translator.translate("Check Connect Error: Response is no JSON format."));
            }
        },
        onFailure: function()
        {
            alert(Translator.translate("Check Connect request failed."));
        }
    });
}