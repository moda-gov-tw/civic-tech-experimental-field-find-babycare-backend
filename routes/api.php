<?php

use App\Http\Controllers\V1\AdministrativeGroupController;
use App\Http\Controllers\V1\AdministrativeGroupDayCareController;
use App\Http\Controllers\V1\AdministrativeGroupMemberController;
use App\Http\Controllers\V1\ApplicationController;
use App\Http\Controllers\V1\ApplicationDayCareDocumentController;
use App\Http\Controllers\V1\ApplicationDocumentController;
use App\Http\Controllers\V1\ApplicationDraftContactController;
use App\Http\Controllers\V1\ApplicationDraftController;
use App\Http\Controllers\V1\ApplicationDraftDayCareController;
use App\Http\Controllers\V1\ApplicationDraftDayCareDocumentController;
use App\Http\Controllers\V1\ApplicationDraftDocumentController;
use App\Http\Controllers\V1\ApplicationDraftInfantController;
use App\Http\Controllers\V1\ApplicationDraftInfantDocumentController;
use App\Http\Controllers\V1\ApplicationInfantDocumentController;
use App\Http\Controllers\V1\DayCareApplicationController;
use App\Http\Controllers\V1\DayCareController;
use App\Http\Controllers\V1\DayCareMemberController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('/administrative-groups')->group(function () {
        Route::get('/', [AdministrativeGroupController::class, 'index'])->name('administrative-groups.index');
        Route::get('/{group}', [AdministrativeGroupController::class, 'show'])->name('administrative-groups.show');
        Route::post('/', [AdministrativeGroupController::class, 'store'])->name('administrative-groups.store');
        Route::patch('/{group}', [AdministrativeGroupController::class, 'update'])->name('administrative-groups.update');
        Route::delete('/{group}', [AdministrativeGroupController::class, 'destroy'])->name('administrative-groups.destroy');
    });

    Route::prefix('/administrative-groups/{group}/members')->scopeBindings()->group(function () {
        Route::get('/', [AdministrativeGroupMemberController::class, 'index'])->name('administrative-groups.members.index');
        Route::post('/', [AdministrativeGroupMemberController::class, 'store'])->name('administrative-groups.members.store');
        Route::get('/{member}', [AdministrativeGroupMemberController::class, 'show'])->name('administrative-groups.members.show');
        Route::patch('/{member}', [AdministrativeGroupMemberController::class, 'update'])->name('administrative-groups.members.update');
        Route::delete('/{member}', [AdministrativeGroupMemberController::class, 'destroy'])->name('administrative-groups.members.destroy');
    });

    Route::prefix('/administrative-groups/{group}/day-cares')->scopeBindings()->group(function () {
        Route::get('/', [AdministrativeGroupDayCareController::class, 'index'])->name('administrative-groups.day-cares.index');
        Route::post('/', [AdministrativeGroupDayCareController::class, 'store'])->name('administrative-groups.day-cares.store');
        Route::get('/{dayCare}', [AdministrativeGroupDayCareController::class, 'show'])->name('administrative-groups.day-cares.show');
        Route::delete('/{dayCare}', [AdministrativeGroupDayCareController::class, 'destroy'])->name('administrative-groups.day-cares.destroy');
    });

    Route::prefix('/day-cares')->group(function () {
        Route::get('/', [DayCareController::class, 'index'])->name('day-cares.index');
        Route::post('/', [DayCareController::class, 'store'])->name('day-cares.store');
        Route::get('/{dayCare}', [DayCareController::class, 'show'])->name('day-cares.show');
        Route::patch('/{dayCare}', [DayCareController::class, 'update'])->name('day-cares.update');
        Route::delete('/{dayCare}', [DayCareController::class, 'destroy'])->name('day-cares.destroy');
    });

    Route::prefix('/day-cares/{dayCare}/members')->scopeBindings()->group(function () {
        Route::get('/', [DayCareMemberController::class, 'index'])->name('day-cares.members.index');
        Route::post('/', [DayCareMemberController::class, 'store'])->name('day-cares.members.store');
        Route::get('/{member}', [DayCareMemberController::class, 'show'])->name('day-cares.members.show');
        Route::patch('/{member}', [DayCareMemberController::class, 'update'])->name('day-cares.members.update');
        Route::delete('/{member}', [DayCareMemberController::class, 'destroy'])->name('day-cares.members.destroy');
    });

    Route::prefix('/application-drafts')->group(function () {
        Route::get('/', [ApplicationDraftController::class, 'index'])->name('application-drafts.index');
        Route::post('/', [ApplicationDraftController::class, 'store'])->name('application-drafts.store');
        Route::get('/{draft}', [ApplicationDraftController::class, 'show'])->name('application-drafts.show');
        Route::patch('/{draft}', [ApplicationDraftController::class, 'update'])->name('application-drafts.update');
        Route::delete('/{draft}', [ApplicationDraftController::class, 'destroy'])->name('application-drafts.destroy');
        Route::post('/{draft}/submit', [ApplicationDraftController::class, 'submit'])->name('application-drafts.submit');
    });

    Route::prefix('/application-drafts/{draft}/infants')->scopeBindings()->group(function () {
        Route::get('/', [ApplicationDraftInfantController::class, 'index'])->name('application-drafts.infants.index');
        Route::post('/', [ApplicationDraftInfantController::class, 'store'])->name('application-drafts.infants.store');
        Route::get('/{infant}', [ApplicationDraftInfantController::class, 'show'])->name('application-drafts.infants.show');
        Route::patch('/{infant}', [ApplicationDraftInfantController::class, 'update'])->name('application-drafts.infants.update');
        Route::delete('/{infant}', [ApplicationDraftInfantController::class, 'destroy'])->name('application-drafts.infants.destroy');
    });

    Route::prefix('/application-drafts/{draft}/day-cares')->scopeBindings()->group(function () {
        Route::get('/', [ApplicationDraftDayCareController::class, 'index'])->name('application-drafts.day-cares.index');
        Route::post('/', [ApplicationDraftDayCareController::class, 'store'])->name('application-drafts.day-cares.store');
        Route::get('/{dayCare}', [ApplicationDraftDayCareController::class, 'show'])->name('application-drafts.day-cares.show');
        Route::patch('/{dayCare}', [ApplicationDraftDayCareController::class, 'update'])->name('application-drafts.day-cares.update');
        Route::delete('/{dayCare}', [ApplicationDraftDayCareController::class, 'destroy'])->name('application-drafts.day-cares.destroy');
    });

    Route::prefix('/application-drafts/{draft}/contacts')->scopeBindings()->group(function () {
        Route::get('/', [ApplicationDraftContactController::class, 'index'])->name('application-drafts.contacts.index');
        Route::post('/', [ApplicationDraftContactController::class, 'store'])->name('application-drafts.contacts.store');
        Route::get('/{contact}', [ApplicationDraftContactController::class, 'show'])->name('application-drafts.contacts.show');
        Route::patch('/{contact}', [ApplicationDraftContactController::class, 'update'])->name('application-drafts.contacts.update');
        Route::delete('/{contact}', [ApplicationDraftContactController::class, 'destroy'])->name('application-drafts.contacts.destroy');
    });

    Route::prefix('/application-drafts/{draft}/documents')->scopeBindings()->group(function () {
        Route::post('/', [ApplicationDraftDocumentController::class, 'store'])->name('application-drafts.documents.store');
        Route::get('/{document}', [ApplicationDraftDocumentController::class, 'show'])->name('application-drafts.documents.show');
        Route::delete('/{document}', [ApplicationDraftDocumentController::class, 'destroy'])->name('application-drafts.documents.destroy');
    });

    Route::prefix('/application-drafts/{draft}/infant-documents')->scopeBindings()->group(function () {
        Route::post('/', [ApplicationDraftInfantDocumentController::class, 'store'])->name('application-drafts.infant-documents.store');
        Route::get('/{infantDocument}', [ApplicationDraftInfantDocumentController::class, 'show'])->name('application-drafts.infant-documents.show');
        Route::delete('/{infantDocument}', [ApplicationDraftInfantDocumentController::class, 'destroy'])->name('application-drafts.infant-documents.destroy');
    });

    Route::prefix('/application-drafts/{draft}/day-care-documents')->scopeBindings()->group(function () {
        Route::post('/', [ApplicationDraftDayCareDocumentController::class, 'store'])->name('application-drafts.day-care-documents.store');
        Route::get('/{dayCareDocument}', [ApplicationDraftDayCareDocumentController::class, 'show'])->name('application-drafts.day-care-documents.show');
        Route::delete('/{dayCareDocument}', [ApplicationDraftDayCareDocumentController::class, 'destroy'])->name('application-drafts.day-care-documents.destroy');
    });

    Route::prefix('/applications')->group(function () {
        Route::get('/', [ApplicationController::class, 'index'])->name('applications.index');
        Route::get('/{application}', [ApplicationController::class, 'show'])->name('applications.show');
        Route::patch('/{application}', [ApplicationController::class, 'update'])->name('applications.update');
        Route::delete('/{application}', [ApplicationController::class, 'destroy'])->name('applications.destroy');
        Route::post('/{application}/withdraw', [ApplicationController::class, 'withdraw'])->name('applications.withdraw');
        Route::post('/{application}/forfeit', [ApplicationController::class, 'forfeit'])->name('applications.forfeit');
        Route::post('/{application}/resubmit', [ApplicationController::class, 'resubmit'])->name('applications.resubmit');
        Route::post('/{application}/register', [ApplicationController::class, 'register'])->name('applications.register');
    });

    Route::prefix('/day-cares/{dayCare}/applications')->scopeBindings()->group(function () {
        Route::get('/', [DayCareApplicationController::class, 'index'])->name('day-cares.applications.index');
        Route::get('/{application}', [DayCareApplicationController::class, 'show'])->name('day-cares.applications.show');
        Route::post('/{application}/return', [DayCareApplicationController::class, 'return'])->name('day-cares.applications.return');
        Route::post('/{application}/approve', [DayCareApplicationController::class, 'approve'])->name('day-cares.applications.approve');
        Route::post('/{application}/accept', [DayCareApplicationController::class, 'accept'])->name('day-cares.applications.accept');
        Route::post('/{application}/reject', [DayCareApplicationController::class, 'reject'])->name('day-cares.applications.reject');
        Route::post('/{application}/enroll', [DayCareApplicationController::class, 'enroll'])->name('day-cares.applications.enroll');
    });

    Route::prefix('/applications/{application}/documents')->scopeBindings()->group(function () {
        Route::get('/{document}', [ApplicationDocumentController::class, 'show'])->name('applications.documents.show');
    });

    Route::prefix('/applications/{application}/infant-documents')->scopeBindings()->group(function () {
        Route::get('/{infantDocument}', [ApplicationInfantDocumentController::class, 'show'])->name('applications.infant-documents.show');
    });

    Route::prefix('/applications/{application}/day-care-documents')->scopeBindings()->group(function () {
        Route::get('/{dayCareDocument}', [ApplicationDayCareDocumentController::class, 'show'])->name('applications.day-care-documents.show');
    });
});
