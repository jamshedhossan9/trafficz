var csrfToken = '{{ csrf_token() }}';
var formSubmission = {};

var listUrlId = function(url){
    return function(id, id2){
        var tempUrl = url;
        if(typeof id === "undefined") return tempUrl;
        tempUrl = tempUrl.replace(':id', id);
        if(typeof id2 === "undefined") return tempUrl;
        tempUrl = tempUrl.replace(':id2', id2);
        return tempUrl;
    }
}

var listUrls = {
    @if(isAdmin())
    adminAddedUsersToGroup: listUrlId('{{ route('admin.addedUsersToGroup',':id') }}'),
    adminDeleteCampaignFromGroup: listUrlId('{{ route('admin.deleteCampaignFromGroup',':id') }}'),
    adminListCreditFromGroup: listUrlId('{{ route('admin.listCreditFromGroup',':id') }}'),
    adminListCreditFromGroup: listUrlId('{{ route('admin.listCreditFromGroup',':id') }}'),
    adminDeleteCreditFromGroup: listUrlId('{{ route('admin.deleteCreditFromGroup',':id') }}'),
    adminInvoicesByUser: listUrlId('{{ route('admin.invoicesByUser',':id') }}'),
    adminInvoiceUpdate: listUrlId('{{ route('admin.invoices.update',':id') }}'),
    @endif
    @if(isUser())
    getAllCampaignGroupStats: '{{ route('user.getAllCampaignGroupStats') }}',
    getCampaignHourlyStats: '{{ route('user.getCampaignHourlyStats') }}',
    @endif
};