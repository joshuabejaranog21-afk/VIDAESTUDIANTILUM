class apiFetcher {
    url = ""
    developer = false

    constructor(url) {
        this.url = url
    }
    changeDeveloper(dev = false) {
        this.developer = dev
        console.log(`Developer = ${dev}`);
    }
    getHTML(src, element) {
        if (this.developer) {
            fetch(src)
                .then(r => r.text())
                .then(resp => console.log(resp))
        } else {
            fetch(src)
                .then(r => r.text())
                .then(resp => {
                    $(element).html(resp)
                })
        }
    }
    // Envío por POST, retorna true o false dependiendo de la respuesta
    sendPost(u, params) {
        var ruta = this.url + u;
        let formdata = new FormData();

        for (const i in params) {
            if (Object.prototype.hasOwnProperty.call(params, i)) {
                const element = params[i];
                formdata.append(element.name, element.value)
            }
        }

        let requestOptions = {
            method: 'POST',
            body: formdata,
            redirect: 'follow'
        };

        if (this.developer) {
            return fetch(ruta, requestOptions)
                .then(response => response.text())
                .then(response => console.log(response));
        } else {
            return fetch(ruta, requestOptions)
                .then(response => response.json())
                .then(response => {
                    console.log(response);
                    if (response.success) {
                        jQuery.notify({
                            title: '¡Exito!',
                            message: response['message']
                        }, {
                            type: 'success',
                            delay: 5000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            },
                        },);
                        return true
                    } else {
                        jQuery.notify({
                            title: '¡Error!',
                            message: response['message']
                        }, {
                            type: 'warning',
                            delay: 5000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            },
                        },);
                        return false
                    }
                });
        }
    }
    // Envío por GET, retorna true o false dependiendo de la respuesta
    sendGet(u) {
        const ruta = this.url + u
        if (this.developer) {
            return fetch(ruta).then(response => response.text());
        } else {
            return fetch(ruta)
                .then(response => response.json())
                .then(response => {
                    console.log(response);
                    if (response.success) {
                        jQuery.notify({
                            title: '¡Exito!',
                            message: response['message']
                        }, {
                            type: 'success',
                            delay: 5000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            },
                        },);
                        return true
                    } else {
                        jQuery.notify({
                            title: '¡Error!',
                            message: response['message']
                        }, {
                            type: 'warning',
                            delay: 5000,
                            placement: {
                                from: 'top',
                                align: 'right',
                            },
                        },);
                        return false
                    }
                });
        }
    }
}
