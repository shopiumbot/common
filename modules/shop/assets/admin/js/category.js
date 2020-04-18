var treeSelector = $('#CategoryTree');
treeSelector.bind('move_node.jstree', function (node, parent) {
    $.ajax({
        type: 'GET',
        url: '/admin/shop/category/move-node',
        data: {
            'id': parent.node.id.replace('node_', ''),
            'ref': parent.parent.replace('node_', ''),
            'position': parent.position
        },
        success: function (data) {
            if (data.success) {
                common.notify(data.message, 'success');
            }
        }
    });
});

treeSelector.bind('rename_node.jstree', function (node, text) {
    if (text.old !== text.text) {
        $.ajax({
            type: 'GET',
            url: "/admin/shop/category/rename-node",
            dataType: 'json',
            data: {
                "id": text.node.id.replace('node_', ''),
                text: text.text
            },
            success: function (data) {
                if (data.success) {
                    common.notify(data.message, 'success');
                } else {
                    common.notify(data.message, 'error');
                }
            }
        });
    }
});

treeSelector.bind('create_node.jstree', function (node, parent, position) {
    $.ajax({
        type: 'GET',
        url: "/admin/shop/category/create-node",
        dataType: 'json',
        data: {
            text: parent.node.text,
            parent_id: parent.parent.replace('node_', '')
        },
        success: function (data) {
            common.notify(data.message, 'success');
        }
    });
});

treeSelector.bind("delete_node.jstree", function (node, parent) {
    $.ajax({
        type: 'GET',
        url: "/admin/shop/category/delete-node",
        data: {
            "id": parent.node.id.replace('node_', '')
        },
        success: function (data) {
            if (data.success) {
                common.notify(data.message, 'success');
            } else {
                common.notify(data.message, 'error');
            }
        }
    });
});

function categorySwitch(node) {
    $.ajax({
        type: 'GET',
        url: "/admin/shop/category/switch-node",
        dataType: 'json',
        data: {
            id: node.id.replace('node_', ''),
        },
        success: function (data) {
            var icon = (data.switch) ? 'icon-eye' : 'icon-eye-close';
            common.notify(data.message, 'success');
            treeSelector.jstree(true).set_icon(node, icon);
        }
    });
}




