<?php

namespace DTApi\Http\Controllers;

use DTApi\Models\Job;
use DTApi\Http\Requests;
use DTApi\Models\Distance;
use Illuminate\Http\Request;
use DTApi\Repository\BookingRepository;
use Exception;

/**
 * Class BookingController
 * @package DTApi\Http\Controllers
 */
class BookingController extends Controller
{

    /**
     * @var BookingRepository
     */
    protected $repository;

    /**
     * BookingController constructor.
     * @param BookingRepository $bookingRepository
     */
    public function __construct(BookingRepository $bookingRepository)
    {
        $this->repository = $bookingRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function index(Request $request)
    {
        try {
            if ($user_id = $request->get('user_id')) {

                $response = $this->repository->getUsersJobs($user_id);
            } elseif ($request->__authenticatedUser->user_type == env('ADMIN_ROLE_ID') || $request->__authenticatedUser->user_type == env('SUPERADMIN_ROLE_ID')) {
                $response = $this->repository->getAll($request);
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        try {
            $job = $this->repository->with('translatorJobRel.user')->find($id);
            return response()->json($job);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|unique:posts|max:255',
                'body' => 'required',
            ]);
            $data = $request->all();

            $response = $this->repository->store($request->__authenticatedUser, $data);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     */
    public function update($id, Request $request)
    {
        try {
            $data = $request->all();
            $cuser = $request->__authenticatedUser;
            $response = $this->repository->updateJob($id, array_except($data, ['_token', 'submit']), $cuser);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * Immediate job email.
     * @param Request $request
     * @return mixed
     */
    public function immediateJobEmail(Request $request)
    {
        try {
            $adminSenderEmail = config('app.adminemail');
            $data = $request->all();
            $response = $this->repository->storeJobEmail($data);
            returnresponse()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * get User Jobs History
     * @param Request $request
     * @return mixed
     */
    public function getHistory(Request $request)
    {
        try {
            if ($user_id = $request->get('user_id')) {
                $response = $this->repository->getUsersJobsHistory($user_id, $request);
                return response()->json($response);
            }
            return response()->json([]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * Accept Job
     * @param Request $request
     * @return mixed
     */
    public function acceptJob(Request $request)
    {
        try {
            $data = $request->all();
            $user = $request->user();

            $response = $this->repository->acceptJob($data, $user);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * Accept job with id
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function acceptJobWithId(Request $request)
    {
        try {
            $data = $request->get('job_id');
            $user = $request->user();
    
            $response = $this->repository->acceptJobWithId($data, $user);
    
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * Cancel Job
     * @param Request $request
     * @return mixed
     */
    public function cancelJob(Request $request)
    {
        try {
            $data = $request->all();
            $user = $request->user();
            $response = $this->repository->cancelJobAjax($data, $user);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * end job
     * @param Request $request
     * @return mixed
     */
    public function endJob(Request $request)
    {
        try {
            $data = $request->all();
            $response = $this->repository->endJob($data);
            return response()->json($response);
            
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * Customer not call
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function customerNotCall(Request $request)
    {
        try {
            $data = $request->all();
            $response = $this->repository->customerNotCall($data);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    /**
     * get potential jobs
     * @param Request $request
     * @return mixed
     */
    public function getPotentialJobs(Request $request)
    {
        try {
            $user = $request->user();
            $response = $this->repository->getPotentialJobs($user);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * Distance Feed
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function distanceFeed(Request $request)
    {
        try {
            $data = $request->all();
            if (isset($data['distance']) && $data['distance'] != "") {
                $distance = $data['distance'];
            } else {
                $distance = "";
            }
            if (isset($data['time']) && $data['time'] != "") {
                $time = $data['time'];
            } else {
                $time = "";
            }
            if (isset($data['jobid']) && $data['jobid'] != "") {
                $jobid = $data['jobid'];
            }

            if (isset($data['session_time']) && $data['session_time'] != "") {
                $session = $data['session_time'];
            } else {
                $session = "";
            }

            if ($data['flagged'] == 'true') {
                if ($data['admincomment'] == '') return "Please, add comment";
                $flagged = 'yes';
            } else {
                $flagged = 'no';
            }

            if ($data['manually_handled'] == 'true') {
                $manually_handled = 'yes';
            } else {
                $manually_handled = 'no';
            }

            if ($data['by_admin'] == 'true') {
                $by_admin = 'yes';
            } else {
                $by_admin = 'no';
            }

            if (isset($data['admincomment']) && $data['admincomment'] != "") {
                $admincomment = $data['admincomment'];
            } else {
                $admincomment = "";
            }
            if ($time || $distance) {

                $affectedRows = Distance::where('job_id', '=', $jobid)->update(array('distance' => $distance, 'time' => $time));
            }

            if ($admincomment || $session || $flagged || $manually_handled || $by_admin) {

                $affectedRows1 = Job::where('id', '=', $jobid)->update(array('admin_comments' => $admincomment, 'flagged' => $flagged, 'session_time' => $session, 'manually_handled' => $manually_handled, 'by_admin' => $by_admin));
            }

            return response()->json(['success' => 'Record updated!']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * Re Open
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function reopen(Request $request)
    {
        try {
            $data = $request->all();
            $response = $this->repository->reopen($data);
            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * Resent Notifications
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendNotifications(Request $request)
    {
        try {
            $data = $request->all();
            $job = $this->repository->find($data['jobid']);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendNotificationTranslator($job, $job_data, '*');
            return response()->json(['success' => 'Push sent']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    /**
     * Sends SMS to Translator
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function resendSMSNotifications(Request $request)
    {
        try {
            $data = $request->all();
            $job = $this->repository->find($data['jobid']);
            $job_data = $this->repository->jobToData($job);
            $this->repository->sendSMSNotificationToTranslator($job);
            return response()->json(['success' => 'SMS sent']);
        } catch (\Exception $e) {
            return response()->json(['success' => $e->getMessage()]);
        }
    }
}
