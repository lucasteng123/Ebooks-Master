$(document).ready(function () {
	$(".tshirt-thumb").click(function () {
		shirtItem = $(this);
		shirtForm = $("#tshirt_update_form");
		props = ['name','colors','price','size','description'];
		props.forEach(function (valu, inde, arry) {
			input = shirtItem.find("[data-name="+valu+"]").first();
			shirtForm.find("[name="+valu+"]").first()
				.val(input.html());
		});
		id = shirtItem.find("[data-name=id]").first().html();
		name = shirtItem.find("[data-name=name]").first().html();
		baseact = shirtForm.find("[data-name=base_action]").html();
		shirtForm.attr('action', baseact + "/" + id);
		$("#elem-that-says-new-tshirt").html("Edit `"+name+"`");
	});
});
