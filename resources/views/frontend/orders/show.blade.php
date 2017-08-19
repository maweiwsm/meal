@extends('layouts.frontend')

@section('content')
	<div class="container">
		<div class="panel panel-warning">
			<div class="panel-heading">
				<h1 class="panel-">饿单详情</h1>
			</div>
			@if (!empty($order))
				<div class="table-responsive">
					<table class="table table-hover table-condensed">
						<tr class="info">
							<td><h4>单号：</h4></td>
							<td><h4>{{ $order->id }}</h4></td>
						</tr>
						<tr class="warning">
							<td><h4>标题：</h4></td>
							<td><h4>{{ $order->title }}</h4></td>
						</tr>
						<tr class="info">
							<td><h4>备注：</h4></td>
							<td><h4>{{ $order->description }}</h4></td>
						</tr>
						<tr class="warning">
							<td><h4>金额：</h4></td>
							<td><h4>100</h4></td>
						</tr>
						<tr class="info">
							<td><h4>状态：</h4></td>
							<td><h4>{{ ($order->status == 1 ? '正在进行' : '已结束') }}</h4></td>
						</tr>
						<tr class="warning">
							<td><h4>发布人：</h4></td>
							<td><h4>{{ $order->description }}</h4></td>
						</tr>
						<tr class="info">
							<td><h4>发布时间：</h4></td>
							<td><h4>{{ $order->created_at }}</h4></td>
						</tr>
						<tr class="warning">
							<td><h4>关闭时间：</h4></td>
							<td><h4>{{ $order->closed_at }}</h4></td>
						</tr>
						<tr class="info">
							<td><h4>操作：</h4></td>
							<td>
								<form action="/task/{{ $order->id }}" method="POST">
									{{ csrf_field() }}
									{{ method_field('DELETE') }}

									<a class="btn btn-success" href="{{ url('/order/'.$order->id.'/edit') }}">编辑</a>
									<button type="submit" id="delete-task-{{ $order->id }}" class="btn btn-danger">
										<i class="fa fa-btn fa-trash"></i>删除
									</button>
								</form>
							</td>
						</tr>
					</table>
				</div>
			@else
				<div class="panel-body">
					<span class="h3">您查看的订单不存在!</span>
				</div>
			@endif
		</div>
	</div>
@endsection
