<div class="profile-card p-20 bg-white rad-10 mt-20">
    <x-admin.widget-title>experiences</x-admin.widget-title>
    <p class="mt-0 mb-20 c-grey fs-15 text-capitalize">what the stages experience i finished </p>

    <x-alerts.alert :success="session('success-add-experience')" :fail="session('fail-add-experience') "/>
    <x-alerts.alert :success="session('success-delete-experience')" :fail="session('fail-delete-experience') "/>
    <x-alerts.alert :success="session('success-edit-experience')" :fail="session('fail-edit-experience') "/>


    <div class="flex align-items-end justify-content-end mb-10">
        <x-primary-button
            x-click
            class="text-capitalize  "
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'add-experience')"
        >
            <i class="fa fa-plus fs-15"></i>
        </x-primary-button>

        @include('admin.profile.add-experience')
    </div>


    @if($admin->experiences->isNotEmpty())
        <ul class="txt-c-mobile experiences">
            @each('admin.profile.experience', $admin->experiences, 'experience')
        </ul>
    @else
        <x-alerts.not-founded :message="'No experience added yet'"/>
    @endif
</div>
