(function () {
    let amisLib = amisRequire('amis');
    let amis = amisRequire('amis/embed');

    // 通过替换下面这个配置来生成不同页面
    let amisJSON = {
        type: 'page',
        title: '用户列表',
        body: {
            type: 'crud',
            syncLocation: false,
            api: {
                url: "admin/admin/search",
                // adaptor: function (payload, response) {
                //   return {
                //     ...payload,
                //     status: payload.status === 200 ? 0 : payload.status
                //   };
                // }
            },
            columnsTogglable: false,
            headerToolbar: ['bulkActions',

                {
                    type: "action",
                    actionType: "dialog",
                    label: "新增管理员",
                    level: "primary",
                    dialog: {
                        body: {
                            type: "form",
                            api: "admin/admin/insert",
                            body: [
                                {type: "hidden", name: "id"},
                                {type: "input-text", name: "account", label: "登录账户", required: true},
                                {type: "input-text", name: "name", label: "姓名", required: true},
                                {type: "input-text", name: "mobile", label: "电话", required: true},
                                {type: "input-text", name: "password", label: "登录密码", required: true},
                                {type: "input-text", name: "type", label: "类型"},
                            ]
                        }
                    }
                }
            ],
            footerToolbar: ["switch-per-page", "statistics", "pagination"],
            columns: [
                {name: "id", label: "ID"},
                {name: "account", label: "登录账户"},
                {name: "name", label: "姓名"},
                {name: "mobile", label: "手机号"},
                {name: "type", label: "账户类别"},
                {name: "登录时间", label: "login_time"},
                {name: "登录IP", label: "login_ip"},
                {name: "上次登录", label: "last_time"},
                {name: "上次登录IP", label: "last_ip"},
                {type: 'datetime', name: "ctime", label: "创建时间"},
                {type: 'status', name: "status", label: "状态"},

                {
                    type: "operation",
                    label: "操作",
                    buttons: [
                        {
                            actionType: "dialog",
                            label: "编辑",
                            level: "primary",
                            dialog: {
                                title: "编辑",
                                body: {
                                    type: "form",
                                    api: "admin/admin/update",
                                    body: [
                                        {type: "hidden", name: "id"},
                                        {type: "input-text", name: "name", label: "姓名", required: true},
                                        {type: "input-text", name: "mobile", label: "电话", required: true},
                                        {type: "input-text", name: "type", label: "类型"},
                                    ]
                                }
                            }
                        },
                        {
                            type: "button",
                            actionType: "dialog",
                            label: "权限",
                            level: "primary",
                            dialog: {
                                title: "设置权限",
                                size: "lg",
                                body: {
                                    type: "form",
                                    api: "admin/Permission/savePermission?type=${type}",

                                    body: {
                                        label: "",
                                        type: "transfer",
                                        name: "menu_id",
                                        selectMode: "tree",
                                        source: {
                                            url: "admin/Permission/getAllMenu?type=${type}",
                                            adaptor: function (payload, response) {
                                                let data = {
                                                    options: payload.data.items,
                                                    value: payload.data.value
                                                }
                                                return {
                                                    status: 0,
                                                    msg: '',
                                                    data: data,
                                                }
                                                return {
                                                    ...payload,
                                                    status: payload.code === 200 ? 0 : payload.code
                                                };
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        {
                            label: "密码重置",
                            type: "button",
                            actionType: "ajax",
                            confirmText: "重置密码为 88888888？",
                            api: "admin/admin/restPwd"
                        },
                        {
                            label: "删除",
                            type: "button",
                            actionType: "ajax",
                            level: "danger",
                            confirmText: "确认要删除？",
                            api: "admin/admin/remove"
                        }
                    ]
                }
            ],
            // type: 'form',
            // mode: 'horizontal',
            // api: '/saveForm',
            // body: [
            //   {
            //     label: 'Name',
            //     type: 'input-text',
            //     name: 'name'
            //   },
            //   {
            //     label: 'Email',
            //     type: 'input-email',
            //     name: 'email'
            //   }
            // ]
        }
    };
    let amisScoped = amis.embed('#root', amisJSON);
})();