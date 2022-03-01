
$(document).on('click', '._delete', (e) => {
    let $this = $(e.currentTarget)
    let id = $this.attr('id')

    dialog('delete_dialog', `Deseja realmente exluir o registro #${id}?`, {
        title: 'Exclusão de registro',
        buttons:{
            yes: {
                text: 'Sim',
                class: 'success',
                callback: () => {
                    window.location.replace(SITE_URL+'/usuarios/delete/'+id);
                }
            },

            not: {
                text: 'Não',
                class: 'danger',
                callback: () => {
                    alert('not')
                }
            }
        }
    })
})