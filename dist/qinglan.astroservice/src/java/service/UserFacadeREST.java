/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package service;

import entity.*;
import java.util.List;
import javax.ejb.Stateless;
import javax.persistence.EntityManager;
import javax.persistence.NoResultException;
import javax.persistence.PersistenceContext;
import javax.persistence.TypedQuery;
import javax.ws.rs.*;
import javax.ws.rs.core.Response;
import utilities.GenerateException;
import java.io.*;
import javax.script.*;

/**
 *
 * @author roxy
 */
@Stateless
@Path("user")
public class UserFacadeREST extends AbstractFacade<User> {
    @PersistenceContext(unitName = "astroservicePU")
    private EntityManager em;

    public UserFacadeREST() {
        super(User.class);
    }

    @POST
    @Consumes({"application/xml", "application/json"})
    public Response create(@DefaultValue("0") @QueryParam("active_user") Long active_user, User entity) {
        boolean hasAccess = false;
        
        //check for public access rights
        String q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'public'";
        TypedQuery<UserAccess> query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
        List<UserAccess> results = query.getResultList();

        for (UserAccess result: results)
        {
            if (result.getCreateAccess() == 1)
            {
                hasAccess = true;
                break;
            }
        }
        
        //if there is no public access defined check for more specific access
        if (active_user > 0)
        {
            //check for a user's general create access rights
            q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'user' AND ua.operator_id="+active_user+" AND ua.user_id IS NULL";
            query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
            results = query.getResultList();

            //there is no entry for operator: ('user',userID) general create access
            if (results.isEmpty())
            {
                //check for all_users access
                q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'all_users' AND ua.user_id IS NULL";
                query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);

                //there should only ever be one entry with operator_type=all_users and target user_id=null
                UserAccess result = query.getSingleResult();
                if (result.getCreateAccess() == 1)
                    hasAccess = true;
                else if (result.getCreateAccess() == -1)
                    hasAccess = false;
            }
            else
            {
                //there should only ever be one entry with operator 'user':userID and target user_id NULL
                UserAccess result = query.getSingleResult();
                if (result.getCreateAccess() == 1)
                    hasAccess = true;
                else if (result.getCreateAccess() == -1)
                    hasAccess = false;
            }
        }
        
        if (hasAccess)
        {
            super.create(entity);
            return Response.status(Response.Status.OK).entity(entity).header("Access-Control-Allow-Origin", "*").build();
        }
        else
        {
            return Response.status(Response.Status.UNAUTHORIZED).header("Access-Control-Allow-Origin", "*").build();
        }
    }

    @PUT
    @Consumes({"application/xml", "application/json"})
    public Response edit(@DefaultValue("0") @QueryParam("active_user") Long active_user, User entity) {
        boolean hasAccess = false;
        
        //check for public access rights
        String q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'public'";
        TypedQuery<UserAccess> query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
        List<UserAccess> results = query.getResultList();

        for (UserAccess result: results)
        {
            if (result.getModifyAccess() == 1)
            {
                hasAccess = true;
                break;
            }
        }
        
        if (active_user > 0)
        {
            //check for general access rights to entity or any user
            q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'all_users'";
            query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
            results = query.getResultList();

            for (UserAccess result: results)
            {
                //this entry defines modify rights of all users, for all users
                if (result.getUserId() == null)
                {
                    if (result.getModifyAccess() == 1)
                        hasAccess = true;
                    else if (result.getModifyAccess() == -1)
                        hasAccess = false;
                }
                //this entry defines modify rights of all users, for entity
                else if (result.getUserId().longValue() == entity.getUserId())
                {
                    if (result.getModifyAccess() == 1)
                    {
                        hasAccess = true;
                        break;
                    }
                    else if (result.getModifyAccess() == -1)
                    {
                        hasAccess = false;
                        break;
                    }
                }
            }

            //check for access rights with operator: ('user', userID)
            // if they exist, these entries override the previously retrieved general access rights
            q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'user' AND ua.operator_id="+active_user;
            query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
            results = query.getResultList();

            for (UserAccess result: results)
            {
                //this entry is a general access right of user:userID, for all users
                if (result.getUserId() == null)
                {
                    if (result.getModifyAccess() == 1)
                    {
                        hasAccess = true;
                    }
                    else if (result.getModifyAccess() == -1)
                    {
                        hasAccess = false;
                    }
                }
                //this entry is a specific access right for user:userID regarding the target user
                //as such, it overrides all other access rights
                else if (result.getUserId().longValue() == entity.getUserId())
                {
                    int modify = result.getModifyAccess();

                    //user:userID has an explicit permission to modify entity
                    if (modify == 1)
                    {
                        hasAccess = true;
                        break;
                    }
                    //user:userID has an explicit restriction for modifying entity
                    else if (modify == -1)
                    {
                        hasAccess = false;
                        break;
                    }
                }
            }
        }
        
        if (hasAccess)
        {
            super.edit(entity);
            return Response.status(Response.Status.OK).entity(entity).header("Access-Control-Allow-Origin", "*").build();
        }
        else
        {
            return Response.status(Response.Status.UNAUTHORIZED).header("Access-Control-Allow-Origin", "*").build();
        }
    }

    @DELETE
    @Path("{id}")
    public Response remove(@DefaultValue("0") @QueryParam("active_user") Long active_user, @PathParam("id") Long id) {
        boolean hasAccess = false;
        
        //check for public access rights
        String q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'public'";
        TypedQuery<UserAccess> query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
        List<UserAccess> results = query.getResultList();

        for (UserAccess result: results)
        {
            if (result.getDeleteAccess() == 1)
            {
                hasAccess = true;
                break;
            }
        }
        
        if (active_user > 0)
        {
            //check for general access rights to user:id or any user
            q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'all_users'";
            query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
            results = query.getResultList();

            for (UserAccess result: results)
            {
                //this entry defines delete rights of all users, for all users
                if (result.getUserId() == null)
                {
                    if (result.getDeleteAccess() == 1)
                    {
                        hasAccess = true;
                    }
                    else if (result.getDeleteAccess() == -1)
                    {
                        hasAccess = false;
                    }
                }
                //this entry defines delete rights of all users, for user:id
                else if (result.getUserId().longValue() == id)
                {
                    if (result.getDeleteAccess() == 1)
                    {
                        hasAccess = true;
                        break;
                    }
                    else if (result.getDeleteAccess() == -1)
                    {
                        hasAccess = false;
                        break;
                    }
                }
            }

            //check for access rights with operator: ('user', userID)
            // if they exist, these entries override the previously retrieved general access rights
            q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'user' AND ua.operator_id="+active_user;
            query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
            results = query.getResultList();

            for (UserAccess result: results)
            {
                //this entry is a general access right of user:userID, for all users
                if (result.getUserId() == null)
                {
                    if (result.getDeleteAccess() == 1)
                    {
                        hasAccess = true;
                    }
                    else if (result.getDeleteAccess() == -1)
                    {
                        hasAccess = false;
                    }
                }
                //this entry is a specific access right for user:userID regarding the target user
                //as such, it overrides all other access rights
                else if (result.getUserId().longValue() == id)
                {
                    int delete = result.getDeleteAccess();

                    //user:userID has an explicit permission to delete user:id
                    if (delete == 1)
                    {
                        hasAccess = true;
                        break;
                    }
                    //user:userID has an explicit restriction for deleting user:id
                    else if (delete == -1)
                    {
                        hasAccess = false;
                        break;
                    }
                }
            }
        }
            
        if (hasAccess)
        {
            super.remove(super.find(id));
            return Response.status(Response.Status.NO_CONTENT).header("Access-Control-Allow-Origin", "*").build();
        }
        else
        {
            return Response.status(Response.Status.UNAUTHORIZED).header("Access-Control-Allow-Origin", "*").build();
        }
    }
    
   /* 
    * This method is not allowed because of privacy concern
    * 
    * @GET
    @Override
    @Produces({"application/xml", "application/json"})
    public List<User> findAll() {
        return super.findAll();
    }
    * 
    */
    
    @GET
    @Path("{from}/{to}")
    @Produces({"application/xml", "application/json"})
    public Response findRange(@DefaultValue("0") @QueryParam("active_user") Long active_user, 
                                @PathParam("from") Integer from, @PathParam("to") Integer to) {
        boolean hasAccess = false;
        
        //check for public access rights
        String q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'public'";
        TypedQuery<UserAccess> query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
        List<UserAccess> results = query.getResultList();

        for (UserAccess result: results)
        {
            if (result.getViewAccess() == 1)
            {
                hasAccess = true;
                break;
            }
        }
        
        if (active_user > 0)
        {
            //check for general access rights for any user
            q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'all_users' AND ua.user_id IS NULL";
            query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
            results = query.getResultList();

            for (UserAccess result: results)
            {
                if (result.getViewAccess() == 1)
                {
                    hasAccess = true;
                }
                else if (result.getViewAccess() == -1)
                {
                    hasAccess = false;
                }
            }

            //check for access rights with operator: ('user', userID), for any user
            // if they exist, these entries override the previously retrieved general access rights
            q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'user' AND ua.operator_id="+active_user+" AND ua.user_id IS NULL";
            query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
            results = query.getResultList();

            for (UserAccess result: results)
            {
                if (result.getViewAccess() == 1)
                {
                    hasAccess = true;
                }
                else if (result.getViewAccess() == -1)
                {
                    hasAccess = false;
                }
            }
        }
            
        if (hasAccess)
        {
            return Response.status(Response.Status.OK).entity(super.findRange(new int[]{from, to})).header("Access-Control-Allow-Origin", "*").build();
        }
        else
        {
            return Response.status(Response.Status.UNAUTHORIZED).header("Access-Control-Allow-Origin", "*").build();
        }
    }

    
    
    /*
     * @desc: search all users giving a USER_ID
     * @param: USER_ID: path parameter; the user id for search
     *         active_user: id for the user who is sending the request
     * @return: one user with a particular user_id
     */
    
    @GET
    @Path("{id}")
    @Produces({/*"application/xml",*/ "application/json"})
    public Response find(@PathParam("id") Long id,
                     @DefaultValue("0")
                     @QueryParam("active_user") Long active_user) throws GenerateException {
        
        /*
         * FIXME: do privacy protection
         * if(active_user==0)
            throw new GenerateException("ACTIVE_USER id is required !");
         *
         */
        
        boolean hasAccess = false;
        
        //check for public access rights
        String q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'public'";
        TypedQuery<UserAccess> query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
        List<UserAccess> results = query.getResultList();

        for (UserAccess result: results)
        {
            if (result.getViewAccess() == 1)
            {
                hasAccess = true;
                break;
            }
        }
        
        if (active_user > 0)
        {
            //check for general access rights to user:id or any user
            q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'all_users'";
            query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
            results = query.getResultList();

            for (UserAccess result: results)
            {
                //this entry defines view rights of all users, for all users
                if (result.getUserId() == null)
                {
                    if (result.getViewAccess() == 1)
                    {
                        hasAccess = true;
                    }
                    else if (result.getViewAccess() == -1)
                    {
                        hasAccess = false;
                    }
                }
                //this entry defines view rights of all users, for user:id
                else if (result.getUserId().longValue() == id)
                {
                    if (result.getViewAccess() == 1)
                    {
                        hasAccess = true;
                        break;
                    }
                    else if (result.getViewAccess() == -1)
                    {
                        hasAccess = false;
                        break;
                    }
                }
            }

            //check for access rights with operator: ('user', userID)
            // if they exist, these entries override the previously retrieved general access rights
            q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'user' AND ua.operator_id="+active_user;
            query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
            results = query.getResultList();

            for (UserAccess result: results)
            {
                //this entry is a general access right of user:userID, for all users
                if (result.getUserId() == null)
                {
                    if (result.getViewAccess() == 1)
                    {
                        hasAccess = true;
                    }
                    else if (result.getViewAccess() == -1)
                    {
                        hasAccess = false;
                    }
                }
                //this entry is a specific access right for user:userID regarding the target user
                //as such, it overrides all other access rights
                else if (result.getUserId().longValue() == id)
                {
                    int view = result.getDeleteAccess();

                    //user:userID has an explicit permission to view user:id
                    if (view == 1)
                    {
                        hasAccess = true;
                        break;
                    }
                    //user:userID has an explicit restriction for viewing user:id
                    else if (view == -1)
                    {
                        hasAccess = false;
                        break;
                    }
                }
            }
        }
        
        if (hasAccess)
        {
            return Response.status(Response.Status.OK).entity(super.find(id)).header("Access-Control-Allow-Origin", "*").build();
        }
        else
        {
            return Response.status(Response.Status.UNAUTHORIZED).header("Access-Control-Allow-Origin", "*").build();
        }
    }
    
    @GET
    @Path("{id}/search")
    @Produces({"application/json"})
    public Response findById(
                     @PathParam("id") Long id,
                     @DefaultValue("0")
                     @QueryParam("active_user") Long active_user) throws GenerateException {
        boolean hasAccess = false;
       
        //check for public access rights
        String q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'public'";
        TypedQuery<UserAccess> query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
        List<UserAccess> results = query.getResultList();

        for (UserAccess result: results)
        {
            if (result.getViewAccess() == 1)
            {
                hasAccess = true;
                break;
            }
        }
        
        if (active_user > 0)
        {
            //check for general access rights to user:id or any user
            q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'all_users'";
            query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
            results = query.getResultList();

            for (UserAccess result: results)
            {
                //this entry defines view rights of all users, for all users
                if (result.getUserId() == null)
                {
                    hasAccess = (result.getViewAccess() == 1);
                }
                //this entry defines view rights of all users, for user:id
                else if (result.getUserId().longValue() == id)
                {
                    hasAccess = (result.getViewAccess() == 1);
                    break;
                }
            }

            //check for access rights with operator: ('user', userID)
            // if they exist, these entries override the previously retrieved general access rights
            q = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'user' AND ua.operator_id="+active_user;
            query = (TypedQuery<UserAccess>)getEntityManager().createQuery(q);
            results = query.getResultList();

            for (UserAccess result: results)
            {
                //this entry is a general access right of user:userID, for all users
                if (result.getUserId() == null)
                {
                    if (result.getViewAccess() == 1)
                    {
                        hasAccess = true;
                    }
                    else if (result.getViewAccess() == -1)
                    {
                        hasAccess = false;
                    }
                }
                //this entry is a specific access right for user:userID regarding the target user
                //as such, it overrides all other access rights
                else if (result.getUserId().longValue() == id)
                {
                    int view = result.getDeleteAccess();

                    //user:userID has an explicit permission to view user:id
                    if (view == 1)
                    {
                        hasAccess = true;
                        break;
                    }
                    //user:userID has an explicit restriction for viewing user:id
                    else if (view == -1)
                    {
                        hasAccess = false;
                        break;
                    }
                }
            }
        }
        
        if (hasAccess)
        {
            return Response.status(Response.Status.OK).entity(super.find(id)).header("Access-Control-Allow-Origin", "*").build();
        }
        else
        {
            return Response.status(Response.Status.UNAUTHORIZED).header("Access-Control-Allow-Origin", "*").build();
        }
    }

    
    //
    // START PRIMITIVE ACTIONS
    //
    
    /*
     * @DESC: search for one particular user with id=actinUserID
     * @PARAM: actingUserId: required parameter
     *         firstName, lastName, userName; default value="";
     * @RETURN: the information about one user if he has a public access or is in the same group with *active_user*
     *         or, all users that have a public view access and users in the same group as *active_user* 
     */
    
    @GET
    @Path("search")
    @Produces({"application/xml", "application/json"})
    public List<User> find(@DefaultValue("0")
                      @QueryParam("active_user") Long active_user,
                      @DefaultValue("")
                      @QueryParam("first_name") String firstName,
                      @DefaultValue("")
                      @QueryParam("last_name") String lastName,
                      @DefaultValue("")
                      @QueryParam("username") String userName) throws GenerateException
    {
         /*
         * FIXME: do privacy protection
         * if(active_user==0)
            throw new GenerateException("ACTIVE_USER id is required !");
         *
         */
        
        String[][] attr={{"u.fname",firstName},{"u.lname",lastName},{"u.username",userName}};
            
        
        String q ="SELECT u from User u";
        Boolean first=true;
         
        for (int i=0; i<attr.length; i++)
        {
            if (!attr[i][1].equals("")) 
            {
                if (first)
                {
                    q=q+" WHERE ";
                    first=false;
                }
                else
                    q =q+" AND ";
                
                q =q+ attr[i][0]+" LIKE \"%"+attr[i][1]+"%\"";
            }
        }
          
        TypedQuery<User> query = getEntityManager().createQuery(q, User.class);
        List<User> resultList = query.getResultList();
        
        //for each result, check user access
        for (User user: resultList)
        {
            boolean hasAccess = false;

            //check for public access rights
            String qq = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'public'";
            TypedQuery<UserAccess> qquery = (TypedQuery<UserAccess>)getEntityManager().createQuery(qq);
            List<UserAccess> results = qquery.getResultList();

            for (UserAccess result: results)
            {
                if (result.getViewAccess() == 1)
                {
                    hasAccess = true;
                    break;
                }
            }   
            
            if (active_user > 0)
            {
                //check for general access rights to user:id or any user
                qq = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'all_users'";
                qquery = (TypedQuery<UserAccess>)getEntityManager().createQuery(qq);
                results = qquery.getResultList();

                for (UserAccess result: results)
                {
                    //this entry defines view rights of all users, for all users
                    if (result.getUserId() == null)
                    {
                        hasAccess = (result.getViewAccess() == 1);
                    }
                    //this entry defines view rights of all users, for user:id
                    else if (result.getUserId().longValue() == user.getUserId())
                    {
                        hasAccess = (result.getViewAccess() == 1);
                        break;
                    }
                }

                //check for access rights with operator: ('user', userID)
                // if they exist, these entries override the previously retrieved general access rights
                qq = "SELECT ua FROM UserAccess ua WHERE ua.operator_type LIKE 'user' AND ua.operator_id="+active_user;
                qquery = (TypedQuery<UserAccess>)getEntityManager().createQuery(qq);
                results = qquery.getResultList();

                for (UserAccess result: results)
                {
                    //this entry is a general access right of user:userID, for all users
                    if (result.getUserId() == null)
                    {
                        if (result.getViewAccess() == 1)
                        {
                            hasAccess = true;
                        }
                        else if (result.getViewAccess() == -1)
                        {
                            hasAccess = false;
                        }
                    }
                    //this entry is a specific access right for user:userID regarding the target user
                    //as such, it overrides all other access rights
                    else if (result.getUserId().longValue() == user.getUserId())
                    {
                        int view = result.getDeleteAccess();

                        //user:userID has an explicit permission to view user:id
                        if (view == 1)
                        {
                            hasAccess = true;
                            break;
                        }
                        //user:userID has an explicit restriction for viewing user:id
                        else if (view == -1)
                        {
                            hasAccess = false;
                            break;
                        }
                    }
                }
            }
            
            if (!hasAccess)
            {
                resultList.remove(user);
            }
        }
        
        return resultList;
    }
    
    @GET
    @Path("count")
    @Produces("text/plain")
    public String countREST() {
        return String.valueOf(super.count());
    }

    //
    // START ASPECTS
    //

    /*
     * @DESC: get annotations made by a particular user
     * @PARAM: active_user; default value is 0 which means it will search for all annotations of all users
     *         firstName; default value is "";
     * @RETURN: all annotations for user *uid* if actingUserId=0 and firstName=""
     */
    @GET
    @Path("{id}/annotations")
    @Produces({"application/xml", "application/json"}) 
    public List<Annotation> findAnnByUserID(
            @PathParam("id") Long user_id,
            @DefaultValue("0")
            @QueryParam("active_user") Long active_user,
            @DefaultValue("")
            @QueryParam("keyword") String keyword,
            @DefaultValue("") 
            @QueryParam("type") String type,
            @DefaultValue("true")
            @QueryParam("usePref") Boolean usePreference) throws GenerateException
    {
        
        /*
         * FIXME: do privacy protection
         * if(active_user==0)
            throw new GenerateException("ACTIVE_USER id is required !");
         *
         */
        
        //TODO Annotation Access
        
        String[][] attr= {{"p.annoValue",keyword},{"p.annoTypeId.annoTypeName", type}}; 
        String q ="SELECT a from Annotation a WHERE a.userId.userId="+user_id;
        
        for(int i=0; i<attr.length; i++)
        {
            if (!attr[i][1].equals("")) 
            {
                q =q+" AND ";

                if (attr[i][1].equals(keyword))
                    q =q+ attr[i][0]+" LIKE \"%"+attr[i][1]+"%\"";
                else
                    q =q+ attr[i][0]+"="+attr[i][1];
            }
        }
        
        List<PrefQT> prefList=null;
        TypedQuery<Annotation> query;
        List<Annotation> result =null;
        
        if (usePreference==true)
        {
            prefList =findAllQTByUserId("SELECT qt FROM PrefQT qt WHERE qt.userId.userId="+active_user+" ORDER BY qt.intensity DESC");
            
            String tempQuery =q;
            for (int i=0; i<prefList.size(); i++)
            {
                tempQuery=q+" AND "+prefList.get(i).getPredicate();
                query =getEntityManager().createQuery(tempQuery, Annotation.class);
                result =query.getResultList();

                if (result.size() > 0)
                    break;
            }
        }
        else
        {
            query =getEntityManager().createQuery(q, Annotation.class);
            result =query.getResultList();
        }
           
        return result;
    }
   
    
    /*
     * @DESC: get preferences made by a particular user
     * @PARAM: active_user: required parameter
     * @RETURN: all annotations for user *uid* if actingUserId=0 and firstName=""
     */
   /* @GET
    @Path("{id}/preferences")
    @Produces({"application/xml", "application/json"}) 
    public List<PrefForUse> findPrefByUserID(
            @PathParam("id") Long id,
            @DefaultValue("0")
            @QueryParam("active_user") Long active_user){
     */   
        /*
         * FIXME: do privacy protection
         * if(active_user==0)
            throw new GenerateException("ACTIVE_USER id is required !");
         *
         */
         
   /*     String qQL ="SELECT ql from PrefForUser p, PrefQL ql"+
                  " WHERE p.userId.userId="+id+" AND "+
                  "(p.prefType=\"QL\" AND p.prefId=ql.prefqlId)";
        String qQT ="SELECT qt from PrefForUser p, PrefQT qt"+
                  " WHERE p.userId.userId="+id+" AND "+
                  "(p.prefType=\"QT\" AND p.prefId=qt.prefqtId)";
                 
        
        TypedQuery<PrefForUser> queryQL =getEntityManager().createQuery(qQL, PrefForUser.class);
        TypedQuery<PrefForUser> queryQT =getEntityManager().createQuery(qQT, PrefForUser.class);
        
        List<PrefForUser> qlResultList =queryQL.getResultList();
        List<PrefForUser> qtResultList =queryQT.getResultList();
        
        boolean success =(qtResultList).addAll(qlResultList);
        
        return qtResultList;
    }*/
    
    
     /*
     * @DESC: get groups where one user is member
     * @PARAM: active_user: optional parameter; the user_id of the acting user
     *         id: optional; group id
     *         name: optional; group name
     * @RETURN: all groups where user *id* is a member
     */
    @GET
    @Path("{id}/groups")
    @Produces({"application/xml", "application/json"}) 
    public List<GroupInfo> findGroupsByUserID(
            @PathParam("id") Long id,
            @DefaultValue("0")
            @QueryParam("active_user") Long active_user,
            @DefaultValue("0")
            @QueryParam("gid") Long group_id,
            @DefaultValue("")
            @QueryParam("name")String group_name) throws GenerateException
    {
        
        /*
         * FIXME: do privacy protection
         * if(active_user==0)
            throw new GenerateException("ACTIVE_USER id is required !");
         *
         */
        
        String q ="SELECT g from GroupInfo g UserBelongGroup ug WHERE ug.groupTarId.groupId =g.groupId AND ug.userSrcId.userId="+id;
        
        if (group_id!=0)
            q=q+" AND g.groupId="+group_id;
        if (!group_name.equals(""))
            q=q+" AND g.groupName LIKE \"%"+group_name+"%\"";
        
        TypedQuery<GroupInfo> query =getEntityManager().createQuery(q, GroupInfo.class);
        List<GroupInfo> allGroups =query.getResultList();
        
        //List<GroupInfo> groupForUser
        return allGroups;
    }
    
    @POST
    @Path("login")
    @Produces({"application/xml", "application/json"})
    public Response doLogin(
            @FormParam("username") String username,
            @FormParam("password") String password
            )
    {
        TypedQuery<User> query = getEntityManager().createNamedQuery("nativeSQL.login",User.class).setParameter(1, "%"+username+"%").setParameter(2, password);
        
        try {
            User r = query.getSingleResult();
            
            return Response.status(Response.Status.OK).entity(r).header("Access-Control-Allow-Origin", "*").build();
        } catch (NoResultException e){
            return Response.status(Response.Status.UNAUTHORIZED).header("Access-Control-Allow-Origin", "*").build();
        }
        
    }
    
    
    @java.lang.Override
    protected EntityManager getEntityManager() {
        return em;
    }
    
    public List<PrefQT> findAllQTByUserId(String q) {
        
        javax.persistence.TypedQuery<PrefQT> query =getEntityManager().createQuery(q, PrefQT.class);
        List<PrefQT> result=query.getResultList();
        
        return result;
    }
}
