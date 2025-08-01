@extends('layout')

@section('content')
    @includeIf('products.error_model')
    <div class="container-fluid">
        <div class="card">
            <div class="card-title text-center border-bottom">
                <p class="fw-bold fs-3">Product</p>
            </div>
            <div class="card-body mt-3 p-2">
                <form id="product-form">
                    <div class="row">
                        <div class="col-12 col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label class="form-label">Product SKU</label>
                                <input type="text" name="sku" value="{{ $product['product_sku'] ?? '' }}"
                                    class="form-control" autocomplete="off" id="sku">
                                <div class="invalid-feedback" id="sku-error"></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label class="form-label">Product name</label>
                                <input type="text" name="name" value="{{ $product['product_name'] ?? '' }}"
                                    class="form-control" autocomplete="off" id="name">
                                <div class="invalid-feedback" id="name-error"></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label class="form-label">Product image</label>
                                <input type="file" name="image" class="form-control" autocomplete="off"
                                    accept="image/*" id="image">

                                <div class="invalid-feedback" id="image-error"></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label class="form-label">Product quantity</label>
                                <input type="number" name="quantity" value="{{ $product['product_quantity'] ?? '' }}"
                                    class="form-control" autocomplete="off" id="quantity">
                                <div class="invalid-feedback" id="quantity-error"></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label class="form-label">Product price</label>
                                <input type="number" name="price" value="{{ $product['product_price'] ?? '' }}"
                                    class="form-control" autocomplete="off" id="price">
                                <div class="invalid-feedback" id="price-error"></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-4 col-lg-3">
                            <div class="mb-3">
                                <label class="form-label">Product tax</label>
                                <div class="input-group">
                                    <input type="number" name="tax" value="{{ $product['product_tax'] ?? '' }}"
                                        class="form-control" placeholder="Tax" autocomplete="off" id="tax">
                                    <span class="input-group-text">%</span>

                                    <div class="invalid-feedback" id="tax-error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 d-flex justify-content-between">
                            <p class="fs-6 fw-bold">Variant Details</p>
                            <button type="button" class="btn btn-success m-2" id="add-variant">Add new</button>
                        </div>
                        <div class="col-12 mb-3" id="variant-details">
                        </div>
                        <div class="col-12" id="messages"> </div>

                        <div class="col-12 text-end">
                            <a href="{{ route('products') }}" class="btn btn-outline-secondary m-2"
                                id="product-form-close">Close</a>
                            <button type="button" class="btn btn-primary m-2" id="product-form-submit">Submit</button>
                        </div>

                    </div>
                </form>

            </div>
        </div>
    </div>

    <script>
        $(() => {

            let variantTypes = @php
                echo json_encode(config('common.productVariantTypes'));
            @endphp;
            const productId = "{{ $id ?? '' }}";

            $.validator.addMethod('filesize', function(value, element, param) {
                return this.optional(element) || (element.files[0].size <= (param * 1024 * 1024))
            }, 'File size must be less than {0} MB');

            $("#product-form").validate({
                rules: {
                    sku: {
                        required: true,
                        minlength: 3,
                        maxlength: 20
                    },
                    name: {
                        required: true,
                        minlength: 3,
                        maxlength: 120
                    },
                    image: {
                        // required: true,
                        accept: "image/jpg,image/jpeg,image/png",
                        filesize: 1
                    },
                    quantity: {
                        required: true,
                        digits: true
                    },
                    price: {
                        required: true,
                        number: true
                    },
                    tax: {
                        required: true,
                        number: true,
                        max: 100
                    }
                },
                messages: {
                    sku: {
                        required: "SKU is required"
                    },
                    name: {
                        required: "Name is required"
                    },
                    image: {
                        required: "Image is required",
                        accept: "Image must be a file of type: jpg, jpeg, png"
                    },
                    quantity: {
                        required: "Quantity is required",
                        digits: "Quantity must be a valid number"
                    },
                    price: {
                        required: "Price is required",
                        number: "Price must be a valid number"
                    },
                    tax: {
                        required: "Tax is required",
                        number: "Tax must be a valid number"
                    }
                },
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                },
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                errorPlacement: function(error, element) {
                    if (element.parent().find('.invalid-feedback')) {
                        element.parent().children('.invalid-feedback').remove()
                    }
                    if (element.parent().hasClass("input-group")) {
                        element.parent().append(error)
                    } else {
                        error.insertAfter(element);
                    }
                }
            });

            $("#product-form-submit").click(() => {
                $("#product-form-submit").prop("disabled", true)

                if ($("#product-form").valid()) {

                    // get values
                    const sku = $("#sku").val();
                    const name = $("#name").val();
                    const image = $("#image").val();
                    const quantity = $("#quantity").val();
                    const price = $("#price").val();
                    const tax = $("#tax").val();
                    let file = document.getElementById('image');
                    if (file) {
                        file = file.files[0]
                    }

                    let inputData = new FormData();

                    if (productId) inputData.append("id", productId)
                    inputData.append("sku", sku)
                    inputData.append("name", name)
                    inputData.append("quantity", quantity)
                    inputData.append("price", price)
                    inputData.append("tax", tax)
                    if (file) inputData.append("image", file)

                    // get variants
                    $(`#variant-details`).children().each(function() {
                        const id = $(this).data("id")
                        const variantId = $(this).data("variant-id")

                        const type = $(`#variant-${id}-type`).val();
                        const value = $(`#variant-${id}-value`).val();

                        inputData.append(`variant[${id}][id]`, variantId)
                        inputData.append(`variant[${id}][type]`, type)
                        inputData.append(`variant[${id}][value]`, value)
                    })

                    $.ajax({
                        url: productId ? "{{ route('product.update') }}" :
                            "{{ route('product.store') }}",
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: inputData,
                        processData: false,
                        contentType: false,
                        success: (res) => {
                            if (res.code == 1) {
                                $("#product-form-submit").prop("disabled", false)

                                for (let [key, value] of Object.entries(res.errors)) {
                                    key = key.replaceAll(".", "-")
                                    console.log("key", key)

                                    $(`#${key}`).addClass("is-invalid");
                                    $(`#${key}-error`) ? $(`#${key}-error`).remove() : ""
                                    console.log("key", key)
                                    $(`#${key}`).after(
                                        `<div id="${key}-error" class="invalid-feedback">${value.join(" ")}</div>`
                                    )
                                }
                            } else if (res.code == 2) {
                                $("#product-form-submit").prop("disabled", false)

                                // show error message
                                const msgContent =
                                    `<div class="alert alert-danger" role="alert">${res.message}</div>`;
                                $("#messages").append(msgContent)

                                setTimeout(() => {
                                    $("#messages").html($("#messages").html()
                                        .replace(msgContent, ""))
                                }, 5000);
                            } else {
                                // show success message
                                const msgContent =
                                    `<div class="alert alert-success" role="alert">${res.message}</div>`;
                                $("#messages").html(msgContent)

                                setTimeout(() => {
                                    location.href = "{{ route('products') }}";
                                }, 1000);
                            }
                        },
                        error: (err) => {
                            $("#product-form-submit").prop("disabled", false)
                            console.log(err)
                        }
                    })
                } else {
                    $("#product-form-submit").prop("disabled", false)
                }

            })

            let variantKey = 0;
            const addVariant = (id = "") => {
                let selectBox = variantTypes.map((v, k) => `<option value="${k}">${v}</option>`).join('');
                if ($(`#variant-details`).children().length < {{ config('common.maxVariants') }}) {
                    $("#variant-details").append(`<div class="card my-2" id="variant-${variantKey}" data-id="${variantKey}" data-variant-id="${id}">
						<div class="card-body row p-2">
							<div class="col-12 col-sm-3 col-md-3 mb-2">
								<select class="form-select" name="variant.${variantKey}.type" id="variant-${variantKey}-type" required>
									<option value="" selected>Select the Type</option>
									${selectBox}
								</select>
							</div>
							<div class="col-12 col-sm-3 col-md-3 mb-2">
								<input type="text" name="variant.${variantKey}.value" value="" class="form-control" placeholder="Value" autocomplete="off" id="variant-${variantKey}-value" required>
							</div>
							<div class="col-12 col-sm-3 col-md-3 text-center">
								<button type="button" class="btn btn-danger btn-sm m-2"  id="delete-${variantKey}-variant" data-id="${variantKey}">
									<i class="bi bi-trash3"></i>
								</button>
							</div>
						</div>
					</div>`)


                    $(`#delete-${variantKey}-variant`).click(function() {

                        // check at least 1 variant is required
                        if ($(`#variant-details`).children().length > 1) {

                            let id = $(this).attr("data-id");
                            $(`#variant-${id}`).remove();
                        } else {

                            $("#errorModal").modal("show")
                            $(".modal-body").html(
                                "<p class='text-danger'>At least 1 variant is required</p>")
                        }
                    })

                    variantKey++;
                } else {
                    $("#errorModal").modal("show")
                    $(".modal-body").html(
                        "<p class='text-danger'>Max {{ config('common.maxVariants') }} variants only</p>")
                }
            }

            $("#add-variant").click(() => addVariant())

            if (productId) {
                @if (!empty($product['variants']))
                    @foreach ($product['variants'] as $row)
                        variantKey = {{ $row['variant_id'] }};
                        addVariant("{{ $row['variant_id'] }}");

                        $(`#variant-${variantKey-1}-type`).val("{{ $row['variant_type'] }}");
                        $(`#variant-${variantKey-1}-value`).val("{{ $row['variant_value'] }}");
                    @endforeach
                @endif
            } else {
                addVariant();
            }
        })
    </script>
@endsection
