@extends('layouts.frontend')

@section('content')
	<div class="container">
		<div class="page-header">
			<h1>创建饿单</h1>
		</div>

		<div class="well">
			<!-- New Task Form -->
			<form action="/order" method="POST" class="form-horizontal">
				{{ csrf_field() }}

				<!-- 饿单标题 -->
				<div class="form-group">
					<label for="title" class="col-md-2 col-sm-3 col-xs-10 control-label">标题</label>
					<div class="col-lg-3 col-md-4 col-sm-5 col-xs-10">
						<input type="text" name="title" class="form-control" value="" placeholder="饿单标题">
					</div>
				</div>

				<!-- 饿单备注 -->
				<div class="form-group">
					<label for="description" class="col-md-2 col-sm-3 col-xs-10 control-label">备注</label>
					<div class="col-lg-6 col-md-8 col-sm-9 col-xs-12">
						<textarea rows="8" class="form-control" name="description" placeholder="饿单描述"></textarea>
					</div>
				</div>

				<!-- 提交按钮 -->
				<div class="form-group">
					<div class="col-md-offset-2 col-sm-offset-3 col-sm-10 col-xs-12">
						<button type="submit" class="btn btn-primary"><i class="fa fa-rocket"></i> 提交</button>
						<button type="reset" class="btn btn-inverse">重置</button>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection
