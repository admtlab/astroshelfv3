/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
package service;

import entity.PrefAnnotation;
import entity.PrefQT;
import java.util.List;
import javax.ejb.Stateless;
import javax.persistence.EntityManager;
import javax.persistence.NoResultException;
import javax.persistence.PersistenceContext;
import javax.persistence.TypedQuery;
import javax.ws.rs.*;
import javax.ws.rs.core.Response;

/**
 *
 * @author roxy
 */
@Stateless
@Path("preference")
public class PrefAnnotationFacadeREST extends AbstractFacade<PrefAnnotation> {
    @PersistenceContext(unitName = "astroservicePU")
    private EntityManager em;

    public PrefAnnotationFacadeREST() {
        super(PrefAnnotation.class);
    }

    @POST
    @Override
    @Consumes({/*"application/xml",*/ "application/json"})
    public void create(PrefAnnotation entity) {
        super.create(entity);
    }

    @POST
    @Path("{id}/add")
    @Consumes({/*"application/xml",*/ "application/json"})
    public Response createAll(PrefQT prefQT,
                    @PathParam("id") Long prefAttrId) {
        
        PrefAnnotation prefAttr =super.find(prefAttrId);
        
        if(prefAttr == null)
            return null;
        
        prefAttr.getPrefQTCollection().add(prefQT);
        getEntityManager().persist(prefQT);
        prefQT.getPrefAnnotationCollection().add(prefAttr);
        
        getEntityManager().flush();
        getEntityManager().refresh(prefAttr);
        getEntityManager().refresh(prefQT);
        
        
        return Response.status(Response.Status.OK).entity(prefAttr).header("Access-Control-Allow-Origin", "*").build();
        
    }

    
    @PUT
    @Override
    @Consumes({"application/xml", "application/json"})
    public void edit(PrefAnnotation entity) {
        super.edit(entity);
    }

    @DELETE
    @Path("{id}")
    public void remove(@PathParam("id") Long id) {
        super.remove(super.find(id));
    }

    @GET
    @Path("{id}")
    @Produces({"application/xml", "application/json"})
    public PrefAnnotation find(@PathParam("id") Long id) {
        return super.find(id);
    }

    @GET
    @Path("search")
    @Produces({/*"application/xml",*/ "application/json"})
    public Response findByInfo(
            @DefaultValue("")
            @QueryParam("attribute")String attribute,
            @DefaultValue("")
            @QueryParam("operator")String operator,
            @DefaultValue("")
            @QueryParam("value")String value) {
        
        String q =" SELECT pref FROM PrefAnnotation pref WHERE pref.attributeName ='"+attribute+"' AND pref.operator='"+operator
                    +"' AND pref.attributeValue='"+value+"'";
        
        TypedQuery<PrefAnnotation> query = getEntityManager().createQuery(q, PrefAnnotation.class);
        PrefAnnotation result;
        
        try{
            result=query.getSingleResult();
        }catch(NoResultException e)
        {
            return Response.status(Response.Status.NOT_FOUND).header("Access-Control-Allow-Origin", "*").build();
        }
        
        
        return Response.status(Response.Status.OK).entity(result).header("Access-Control-Allow-Origin", "*").build();
    }
    
    @GET
    @Override
    @Produces({"application/xml", "application/json"})
    public List<PrefAnnotation> findAll() {
        return super.findAll();
    }

    @GET
    @Path("{from}/{to}")
    @Produces({"application/xml", "application/json"})
    public List<PrefAnnotation> findRange(@PathParam("from") Integer from, @PathParam("to") Integer to) {
        return super.findRange(new int[]{from, to});
    }

    @GET
    @Path("count")
    @Produces("text/plain")
    public String countREST() {
        return String.valueOf(super.count());
    }

    @java.lang.Override
    protected EntityManager getEntityManager() {
        return em;
    }
    
}
