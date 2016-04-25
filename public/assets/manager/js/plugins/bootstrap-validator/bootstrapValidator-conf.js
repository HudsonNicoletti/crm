$(document).ready(function() {

	$('.validator-form').each(function(){
		var $this = $(this),
				formFields = {};

		$this.find('[data-validate]').each(function(){
			var name = $(this).attr('name'),
					options = {},
					validate = {
						empty: $(this).data('empty'),
						email: $(this).data('email'),
						date: $(this).data('date')
					};

			if(validate.empty){ options.notEmpty 			= { message: validate.empty , } }
			if(validate.email){ options.emailAddress	= { message: validate.email , } }
			if(validate.date){ 	options.date 					= { message: validate.date  ,format: 'DD/MM/YYYY' } }

			formFields[name] = {
				validators: options
			};

		})
		.promise()
		.then(function(){
			$this.bootstrapValidator({
					framework: 'bootstrap',
					message: 'This value is not valid',
					feedbackIcons: {
							valid: 'glyphicon glyphicon-ok',
							invalid: 'glyphicon glyphicon-remove',
							validating: 'glyphicon glyphicon-refresh'
					},
					fields: formFields
			})
			.on('error.form.bv', function(e) {

			})
			.on('success.form.bv', function(e) {

				var $this   = $(e.target),
	          action  = $this.attr("action"),
	          method  = $this.attr("method"),
	          $alert  = $("div[role='alert']"),
	          inputs  = $this.find("input:not(:file):not(:submit) , textarea, select"),
	          files   = $this.find("input:file"),
	          content = new FormData( $this );

	          //  Loop & append inputs
	          for( var i = 0;  i < inputs.length ; ++i )
	          {
	              content.append( $(inputs[i]).attr("name") , $(inputs[i]).val() ); // Add all fields automatic
	          }

	          //  Loop & append files with file data
	          if( files.length  ) {
	              for( var i = 0;  i < files.length ; ++i )
	              {
	                  if(files[i].files[i] != undefined)
	                  {
	                      content.append(files.eq(i).attr("name"), files[i].files[i], files[i].files[i].name );// add files if exits
	                  }
	              }
	          }

	          //  Submit data
	          $.ajax({
	              url:  action,           //  Action  ( PHP SCRIPT )
	              type: method,           //  Method
	              data: content,          //  Data Created
	              processData: false,     //  Tell jQuery not to process the data
	              contentType: false,     //  Tell jQuery not to set contentType
	              dataType: "json",       //  Accept JSON response
	              cache: false,           //  Disale Cashing
								complete: function(data)
								{
									$("button[type='submit']").removeAttr("disabled");
								},
	              success: function( response )
	              {
									var alertClass = (response.status ? "alert-success" : "alert-danger");

									$alert.removeClass("alert-success alert-danger alert-warning alert-info hidden")
												.addClass(alertClass)
												.find("#title").html(response.title)
												.parent()
												.find("#desc").html(response.text)

									if(response.redirect)
									{
										setTimeout(function(){
											window.location.href = response.redirect
										},response.time);
									}

	              }
	          });

	      return false;

			});

		});
	});

});
