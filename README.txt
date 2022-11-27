
What makes it amazing code.
==========================
When we use try catch the exception will be handle.
I'd use the try/catch block when the normal path through the code should proceed without error unless there are truly some exceptional conditions -- like the server being down, your credentials being expired or incorrect. I wouldn't necessarily use it to handle non-exceptional errors -- say like the current user not being in the correct role. That is, when you can reasonably expect and handle an error that is not an exceptional condition, I think you should do your checks.

A try/catch block is an excellent way to handle it as you normally expect the query to succeed. On the other hand, you'll probably want to check that the contents of result are what you expect with control flow logic rather than just attempting to use data that may not be valid for your purpose.

One thing that you want to look out for is sloppy use of try/catch. Try/catch shouldn't be used to protect yourself from bad programming -- the "I don't know what will happen if I do this so I'm going to wrap it in a try/catch and hope for the best" kind of programming. Typically you'll want to restrict the kinds of exceptions you catch to those that are not related to the code itself (server down, bad credentials, etc.) so that you can find and fix errors that are code related (null pointers, etc.).

Code refactor:
I have use try catch in that (that is explained above) and some changes in response return and authenticated user for preventing errors.

what makes it terrible.
=========================
The kinds of exceptions you catch to those that are not related to the code itself (server down, bad credentials, etc.) can not identified without using try catch. 

Thanks.