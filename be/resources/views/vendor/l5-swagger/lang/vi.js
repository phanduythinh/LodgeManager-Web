(function () {
    var vi = {
        "info": {
            "title": "Thông tin",
            "description": "Mô tả",
            "version": "Phiên bản",
            "contact": "Liên hệ",
            "termsOfService": "Điều khoản dịch vụ",
            "license": "Giấy phép",
            "licenseUrl": "URL giấy phép"
        },
        "operations": {
            "get": "Lấy",
            "post": "Tạo mới",
            "put": "Cập nhật",
            "delete": "Xóa",
            "patch": "Cập nhật một phần",
            "parameters": "Tham số",
            "responses": "Phản hồi",
            "summary": "Tóm tắt",
            "description": "Mô tả",
            "operationId": "ID thao tác",
            "consumes": "Định dạng nhận",
            "produces": "Định dạng trả về",
            "schemes": "Giao thức",
            "deprecated": "Đã lỗi thời",
            "security": "Bảo mật"
        },
        "parameters": {
            "name": "Tên",
            "in": "Vị trí",
            "description": "Mô tả",
            "required": "Bắt buộc",
            "type": "Kiểu dữ liệu",
            "format": "Định dạng",
            "default": "Mặc định",
            "enum": "Danh sách giá trị",
            "items": "Phần tử",
            "collectionFormat": "Định dạng tập hợp",
            "minimum": "Giá trị nhỏ nhất",
            "maximum": "Giá trị lớn nhất",
            "exclusiveMinimum": "Giá trị nhỏ nhất (không bao gồm)",
            "exclusiveMaximum": "Giá trị lớn nhất (không bao gồm)",
            "minLength": "Độ dài tối thiểu",
            "maxLength": "Độ dài tối đa",
            "pattern": "Mẫu",
            "minItems": "Số phần tử tối thiểu",
            "maxItems": "Số phần tử tối đa",
            "uniqueItems": "Phần tử duy nhất",
            "multipleOf": "Bội số của"
        },
        "responses": {
            "code": "Mã",
            "description": "Mô tả",
            "headers": "Headers",
            "examples": "Ví dụ",
            "schema": "Schema"
        },
        "models": {
            "properties": "Thuộc tính",
            "required": "Bắt buộc",
            "type": "Kiểu dữ liệu",
            "format": "Định dạng",
            "default": "Mặc định",
            "enum": "Danh sách giá trị",
            "items": "Phần tử",
            "collectionFormat": "Định dạng tập hợp",
            "minimum": "Giá trị nhỏ nhất",
            "maximum": "Giá trị lớn nhất",
            "exclusiveMinimum": "Giá trị nhỏ nhất (không bao gồm)",
            "exclusiveMaximum": "Giá trị lớn nhất (không bao gồm)",
            "minLength": "Độ dài tối thiểu",
            "maxLength": "Độ dài tối đa",
            "pattern": "Mẫu",
            "minItems": "Số phần tử tối thiểu",
            "maxItems": "Số phần tử tối đa",
            "uniqueItems": "Phần tử duy nhất",
            "multipleOf": "Bội số của"
        },
        "security": {
            "title": "Bảo mật",
            "type": "Kiểu",
            "name": "Tên",
            "in": "Vị trí",
            "description": "Mô tả",
            "flow": "Luồng",
            "authorizationUrl": "URL xác thực",
            "tokenUrl": "URL token",
            "scopes": "Phạm vi"
        }
    };

    if (typeof window.SwaggerTranslator === 'undefined') {
        window.SwaggerTranslator = {};
    }

    window.SwaggerTranslator.translate = function () {
        var $i18n = $('.swagger-ui .i18n');
        $i18n.each(function () {
            var $this = $(this);
            var key = $this.attr('data-i18n');
            if (key) {
                var value = key.split('.').reduce(function (obj, i) {
                    return obj[i];
                }, vi);
                if (value) {
                    $this.text(value);
                }
            }
        });
    };

    window.SwaggerTranslator.translate();
})(); 