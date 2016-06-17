{{#create}}
<!-- CREATE -->
<div class="modal fade" id="create" tabindex="-1" role="dialog" aria-labelledby="create" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="create">Cadastrar Tarefa</h4>
      </div>
        <form role="form" method="post" accept-charset="utf8" action="{{action}}" class="validator-form" >
        <div class="modal-body">

          <div class="alert hidden fade in" id="createBox">
            <h4 id="title"></h4>
            <p id="desc"></p>
          </div>

          <div class="row">
            {{#inputs}}
            <div class="col-md-12">
              <label class="control-label">{{{title}}}</label>
              <div class="form-group has-feedback">
                {{{input}}}
              </div>
            </div>
            {{/inputs}}
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Cadastrar</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{/create}}
{{#modify}}
<!-- MODIFY -->
<div class="modal fade" id="modify" tabindex="-1" role="dialog" aria-labelledby="modify" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="modify">Alterar Informações</h4>
      </div>
        <form role="form" method="post" accept-charset="utf8" action="{{action}}" class="validator-form" >
        <div class="modal-body">

          <div class="alert hidden fade in" id="updateBox">
            <h4 id="title"></h4>
            <p id="desc"></p>
          </div>

          <div class="row">
            {{#inputs}}
            <div class="col-md-12">
              <label class="control-label">{{{title}}}</label>
              <div class="form-group has-feedback">
                {{{input}}}
              </div>
            </div>
            {{/inputs}}
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-primary">Alterar</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{/modify}}
{{#remove}}
<!-- REMOVE -->
<div class="modal fade" id="remove" tabindex="-1" role="dialog" aria-labelledby="remove" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="remove">Confrimação</h4>
      </div>
        <form role="form" method="post" accept-charset="utf8" action="{{action}}" class="validator-form" >
        <div class="modal-body">

          {{#alert}}
          <div class="alert alert-info fade in">
            <h4 id="title">{{title}}</h4>
            <p id="desc">{{desc}}</p>
          </div>
          {{/alert}}

          <div class="alert hidden fade in" id="removeBox">
            <h4 id="title"></h4>
            <p id="desc"></p>
          </div>

          <div class="row">
            {{#inputs}}
            <div class="col-md-12">
              <label class="control-label">{{{title}}}</label>
              <div class="form-group has-feedback">
                {{{input}}}
              </div>
            </div>
            {{/inputs}}
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-danger">Remover</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{/remove}}
{{#view}}
<!-- VIEW -->
<div class="modal fade" id="view" tabindex="-1" role="dialog" aria-labelledby="view" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <h4 class="modal-title" id="view">Informações</h4>
      </div>
        <div class="modal-body">

          <div class="row">
            {{#inputs}}
            <div class="col-md-12">
              <label class="control-label">{{{title}}}</label>
              <div class="form-group has-feedback">
                {{{input}}}
              </div>
            </div>
            {{/inputs}}
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
        </div>
      </form>
    </div>
  </div>
</div>
{{/view}}
